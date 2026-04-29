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
        'title',
        'condition',
        'starting_bid_yen',
        'current_bid_yen',
        'shipping_fee_yen',
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
    ];

    public function getTotalEstimatedYenAttribute(): int
    {
        return (int) $this->current_bid_yen + (int) ($this->shipping_fee_yen ?? 0);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(WatchlistItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['q'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%'.$search.'%');
        });

        $query->when($filters['min_price'] ?? null, function ($query, $price) {
            $query->where('current_bid_yen', '>=', $price);
        });

        $query->when($filters['max_price'] ?? null, function ($query, $price) {
            $query->where('current_bid_yen', '<=', $price);
        });

        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });

        $query->when($filters['sort'] ?? null, function ($query, $sort) {
            match ($sort) {
                'price_asc' => $query->orderBy('current_bid_yen', 'asc'),
                'price_desc' => $query->orderBy('current_bid_yen', 'desc'),
                'ends_soon' => $query->orderBy('ends_at', 'asc'),
                'newest' => $query->orderBy('created_at', 'desc'),
                'bid_count' => $query->orderBy('bid_count', 'desc'),
                default => $query->orderBy('ends_at', 'asc'),
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
            $query->orderBy('ends_at', 'asc');
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
        return $query->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now());
    }

    /**
     * Get time remaining or status
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

        if ($diff->days > 0) {
            return "{$diff->days}d {$diff->h}h";
        }

        if ($diff->h > 0) {
            return "{$diff->h}h {$diff->i}m";
        }

        return "{$diff->i}m";
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
        'shipping_fee_yen' => 'integer',
        'bid_count' => 'integer',
        'seller_rating' => 'float',
        'view_count' => 'integer',
        'bidder_confirmed_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'winner_user_id' => 'integer',
        'winning_bid_id' => 'integer',
    ];
}
