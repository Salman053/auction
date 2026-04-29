<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_account_id',
        'shipping_rate_id',
    ];

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(WatchlistItem::class);
    }

    public function shippingRate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShippingRate::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'two_factor_enabled_at' => 'datetime',
            'role' => UserRole::class,
            'password' => 'hashed',
        ];
    }
}
