<?php

namespace App\Models;

use Database\Factories\AuctionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auction extends Model
{
    /** @use HasFactory<AuctionFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'yahoo_auction_id',
        'yahoo_category_id',
        'title',
        'condition',
        'starting_bid_yen',
        'current_bid_yen',
        'bid_count',
        'status',
        'starts_at',
        'ends_at',
        'seller_name',
        'yahoo_seller_id',
        'seller_rating',
        'thumbnail_url',
        'image_urls',
        'raw',
        'last_synced_at',
        'view_count',
        'shipment_status',
        'bidder_confirmed_at',
        'admin_approved_at',
        'winner_user_id',
        'winning_bid_id',
        'auto_extension',
        'yahoo_watcher_count',
    ];

    public function getTotalEstimatedYenAttribute(): int
    {
        return (int) $this->current_bid_yen;
    }

    public function getImageUrlsAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        $urls = is_string($value) ? json_decode($value, true) : $value;

        if (! is_array($urls)) {
            return [];
        }

        return array_values(array_filter($urls, function ($url) {
            $lowerUrl = strtolower($url);
            if (str_contains($lowerUrl, 'buyee') || 
                str_contains($lowerUrl, 'banner') || 
                str_contains($lowerUrl, 'promo') || 
                str_contains($lowerUrl, 'logo') ||
                (str_contains($lowerUrl, 's.yimg.jp') && !str_contains($lowerUrl, 'auc'))) {
                return false;
            }
            return true;
        }));
    }

    public function getThumbnailUrlAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $lowerUrl = strtolower($value);
        if (str_contains($lowerUrl, 'buyee') || 
            str_contains($lowerUrl, 'banner') || 
            str_contains($lowerUrl, 'promo') || 
            str_contains($lowerUrl, 'logo') ||
            (str_contains($lowerUrl, 's.yimg.jp') && !str_contains($lowerUrl, 'auc'))) {
            return null;
        }

        return $value;
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(WatchlistItem::class);
    }

    public function watchers()
    {
        return $this->belongsToMany(User::class, 'watchlist_items');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'ending_soon'])
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['q'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%'.$search.'%');
        });

        $query->when($filters['category'] ?? null, function ($query, $categoryId) {
            $query->where('yahoo_category_id', $categoryId);
        });

        $query->when($filters['min_price'] ?? null, function ($query, $price) {
            $query->where('current_bid_yen', '>=', $price);
        });

        $query->when($filters['max_price'] ?? null, function ($query, $price) {
            $query->where('current_bid_yen', '<=', $price);
        });

        $status = $filters['status'] ?? 'active';

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'finished') {
            $query->where(function ($q) {
                $q->where('status', 'closed')
                    ->orWhere('ends_at', '<=', now());
            });
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        // Always prioritize auctions that have images fetched
        $query->orderByRaw('CASE WHEN image_urls IS NOT NULL THEN 1 ELSE 0 END DESC');

        $query->when($filters['sort'] ?? null, function ($query, $sort) {
            match ($sort) {
                'price_asc' => $query->orderBy('current_bid_yen', 'asc'),
                'price_desc' => $query->orderBy('current_bid_yen', 'desc'),
                'ends_soon' => $query->orderBy('ends_at', 'asc'),
                'newest' => $query->orderBy('created_at', 'desc'),
                'bid_count' => $query->orderBy('bid_count', 'desc'),
                'random' => $query->inRandomOrder(),
                default => $query->inRandomOrder(),
            };
        });

        $query->when($filters['unique_sellers'] ?? null, function ($query) {
            $query->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('auctions')
                    ->whereNull('deleted_at')
                    ->groupBy('yahoo_seller_id');
            });
        });

        if (! isset($filters['sort'])) {
            $query->inRandomOrder();
        }

        return $query;
    }

    /**
     * Check if auction has ended
     */
    public function hasEnded(): bool
    {
        if (! $this->ends_at) {
            return false;
        }

        return $this->ends_at->isPast();
    }

    /**
     * Scope for auctions that should be closed
     */
    public function scopeShouldClose($query)
    {
        return $query->whereIn('status', ['active', 'closed'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now());
    }

    /**
     * Get time remaining or status formatted like Yahoo Auctions
     */
    public function getTimeRemainingAttribute(): string
    {
        if (! $this->ends_at) {
            return 'No end date';
        }

        if ($this->ends_at->isPast()) {
            return 'Ended';
        }

        $diff = now()->diff($this->ends_at);

        // Match Yahoo's "Ceiling" style display for units
        if ($diff->days > 0) {
            // If it's e.g. 2 days and 1 hour, Yahoo often shows "2日" but sometimes "3日"
            // depending on exact end time. We'll show days and hours for clarity.
            // But to avoid the "1 day less" issue, we ensure we don't floor prematurely.
            return "{$diff->days}d ".($diff->h > 0 ? "{$diff->h}h" : '');
        }

        if ($diff->h > 0) {
            return "{$diff->h}h {$diff->i}m";
        }

        return "{$diff->i}m ".($diff->s > 0 ? "{$diff->s}s" : '');
    }

    public function scopeEndingSoon($query, $hours = 24)
    {
        return $query->where('ends_at', '<=', now()->addHours($hours))
            ->where('ends_at', '>', now());
    }

    /**
     * @return array<string, string>
     */
    protected $casts = [
        'image_urls' => 'array',
        'raw' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'starting_bid_yen' => 'integer',
        'current_bid_yen' => 'integer',
        'bid_count' => 'integer',
        'seller_rating' => 'float',
        'view_count' => 'integer',
        'bidder_confirmed_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'winner_user_id' => 'integer',
        'winning_bid_id' => 'integer',
        'auto_extension' => 'boolean',
        'yahoo_watcher_count' => 'integer',
    ];
}
