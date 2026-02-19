<?php

namespace App\Domain\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database',
        'settings',
        'is_active',
        'expires_at',
        'plan',
        'trial_ends_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Relation: Un tenant a plusieurs utilisateurs
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    /**
     * Relation: Un tenant a plusieurs clients
     */
    public function clients()
    {
        return $this->hasMany(\App\Domain\Client\Models\Client::class);
    }

    /**
     * Relation: Un tenant a plusieurs factures
     */
    public function invoices()
    {
        return $this->hasMany(\App\Domain\Invoice\Models\Invoice::class);
    }

    /**
     * Scope: Uniquement les tenants actifs
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
     * Vérifier si le tenant est actif
     */
    public function isActive(): bool
    {
        return $this->is_active && 
               (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    /**
     * Obtenir une configuration spécifique
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Définir une configuration
     */
    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        $this->save();
    }
}
