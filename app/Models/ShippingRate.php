<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingRate extends Model
{
    protected $fillable = [
        'name',
        'fee_yen',
        'country',
        'port',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
