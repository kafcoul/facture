<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Domain\Tenant\Models\Tenant;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'key',
        'secret_hash',
        'permissions',
        'is_active',
        'last_used_at',
        'usage_count',
        'rate_limit_per_minute',
        'expires_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'secret_hash',
    ];

    /**
     * Générer une nouvelle clé API
     */
    public static function generate(array $attributes): array
    {
        $key = 'inv_' . Str::random(32);
        $secret = Str::random(48);

        $apiKey = static::create(array_merge($attributes, [
            'key' => $key,
            'secret_hash' => hash('sha256', $secret),
        ]));

        return [
            'api_key' => $apiKey,
            'key' => $key,
            'secret' => $secret,
            'full_key' => "{$key}:{$secret}",
        ];
    }

    /**
     * Relation: La clé appartient à un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation: La clé appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifier le secret
     */
    public function verifySecret(string $secret): bool
    {
        return hash('sha256', $secret) === $this->secret_hash;
    }

    /**
     * Vérifier si la clé est valide
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Enregistrer une utilisation
     */
    public function recordUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Vérifier une permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        // Toutes les permissions
        if (in_array('*', $permissions)) {
            return true;
        }

        // Permission exacte
        if (in_array($permission, $permissions)) {
            return true;
        }

        // Permission wildcard
        $parts = explode('.', $permission);
        if (count($parts) === 2) {
            $wildcard = $parts[0] . '.*';
            if (in_array($wildcard, $permissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Révoquer la clé
     */
    public function revoke(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Scope: Clés actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    /**
     * Masquer la clé pour l'affichage
     */
    public function getMaskedKeyAttribute(): string
    {
        return substr($this->key, 0, 8) . '...' . substr($this->key, -4);
    }
}
