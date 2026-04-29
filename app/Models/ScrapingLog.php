<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapingLog extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'run_uuid',
        'proxy_id',
        'status',
        'started_at',
        'ended_at',
        'auctions_created',
        'auctions_updated',
        'auctions_closed',
        'error_message',
        'meta',
    ];

    public function proxy(): BelongsTo
    {
        return $this->belongsTo(Proxy::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
