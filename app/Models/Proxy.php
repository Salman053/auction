<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proxy extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'scheme',
        'host',
        'port',
        'username',
        'password',
        'country',
        'is_active',
        'success_count',
        'failure_count',
        'avg_response_ms',
        'last_used_at',
        'last_checked_at',
        'disabled_until',
        'last_error',
        'meta',
    ];

    public function scrapingLogs(): HasMany
    {
        return $this->hasMany(ScrapingLog::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'last_checked_at' => 'datetime',
            'disabled_until' => 'datetime',
            'meta' => 'array',
        ];
    }
}
