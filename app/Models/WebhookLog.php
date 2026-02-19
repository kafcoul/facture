<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'gateway',
        'event_type',
        'payload',
        'processed',
        'response',
        'ip_address',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'processed' => 'boolean',
    ];

    /**
     * Log a webhook event.
     */
    public static function log(string $gateway, string $eventType, array $payload, ?string $ipAddress = null): self
    {
        return static::create([
            'gateway' => $gateway,
            'event_type' => $eventType,
            'payload' => $payload,
            'processed' => true,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Scope: Filter by gateway
     */
    public function scopeForGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * Scope: Recent webhooks
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
