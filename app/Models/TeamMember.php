<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Tenant\Models\Tenant;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'invited_by',
        'role',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    /**
     * Rôles disponibles avec leurs permissions
     */
    const ROLES = [
        'owner' => [
            'label' => 'Propriétaire',
            'description' => 'Accès complet, peut gérer l\'équipe et facturation',
            'permissions' => ['*'],
        ],
        'admin' => [
            'label' => 'Administrateur',
            'description' => 'Peut gérer les clients, factures et membres',
            'permissions' => ['clients.*', 'invoices.*', 'products.*', 'team.view', 'team.invite'],
        ],
        'member' => [
            'label' => 'Membre',
            'description' => 'Peut créer et gérer les factures',
            'permissions' => ['clients.view', 'clients.create', 'invoices.*', 'products.view'],
        ],
        'viewer' => [
            'label' => 'Observateur',
            'description' => 'Accès en lecture seule',
            'permissions' => ['clients.view', 'invoices.view', 'products.view'],
        ],
    ];

    /**
     * Relation: Le membre appartient à un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation: Le membre est un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation: Invité par un utilisateur
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Vérifier si le membre a une permission
     */
    public function hasPermission(string $permission): bool
    {
        $rolePermissions = self::ROLES[$this->role]['permissions'] ?? [];
        
        // Owner a toutes les permissions
        if (in_array('*', $rolePermissions)) {
            return true;
        }

        // Vérifier permission exacte
        if (in_array($permission, $rolePermissions)) {
            return true;
        }

        // Vérifier permission wildcard (ex: invoices.*)
        $parts = explode('.', $permission);
        if (count($parts) === 2) {
            $wildcard = $parts[0] . '.*';
            if (in_array($wildcard, $rolePermissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si le membre est propriétaire
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Vérifier si le membre est admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }

    /**
     * Scope: Membres actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtenir le libellé du rôle
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role]['label'] ?? $this->role;
    }
}
