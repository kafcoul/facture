<?php

namespace App\Domain\Client\Models;

use App\Domain\Tenant\Models\Tenant;
use App\Domain\Invoice\Models\Invoice;
use App\Infrastructure\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\ClientFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'user_id',
        'company',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'tax_id',
        'currency',
        'language',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relation: Un client appartient à un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation: Un client appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Relation: Un client a plusieurs factures
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scope: Uniquement les clients actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtenir le nom complet
     */
    public function getFullNameAttribute(): string
    {
        return $this->company ? "{$this->company} ({$this->name})" : $this->name;
    }

    /**
     * Obtenir le total des factures impayées
     */
    public function getUnpaidInvoicesTotal(): float
    {
        return $this->invoices()
                    ->whereIn('status', ['draft', 'sent', 'viewed', 'overdue'])
                    ->sum('total');
    }
}
