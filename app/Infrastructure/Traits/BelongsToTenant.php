<?php

namespace App\Infrastructure\Traits;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait pour les modèles qui appartiennent à un tenant
 * Applique automatiquement un scope global pour isoler les données par tenant
 */
trait BelongsToTenant
{
    /**
     * Boot le trait
     */
    protected static function bootBelongsToTenant()
    {
        // Appliquer le scope global pour filtrer automatiquement par tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where($builder->getQuery()->from . '.tenant_id', auth()->user()->tenant_id);
            }
        });

        // Définir automatiquement le tenant_id lors de la création
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * Relation: Appartient à un tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope pour requêter sans le filtre tenant (admin uniquement)
     */
    public function scopeWithoutTenantScope(Builder $query)
    {
        return $query->withoutGlobalScope('tenant');
    }

    /**
     * Scope pour un tenant spécifique
     */
    public function scopeForTenant(Builder $query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
