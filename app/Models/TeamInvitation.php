<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Domain\Tenant\Models\Tenant;

class TeamInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'invited_by',
        'email',
        'name',
        'role',
        'token',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            // Générer un token unique
            if (empty($invitation->token)) {
                $invitation->token = Str::random(64);
            }
            
            // Définir l'expiration (7 jours par défaut)
            if (empty($invitation->expires_at)) {
                $invitation->expires_at = now()->addDays(7);
            }
        });
    }

    /**
     * Relation: L'invitation appartient à un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation: Invité par un utilisateur
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Vérifier si l'invitation est en attente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Vérifier si l'invitation est expirée
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status === 'expired';
    }

    /**
     * Vérifier si l'invitation est valide
     */
    public function isValid(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    /**
     * Accepter l'invitation
     */
    public function accept(User $user): TeamMember
    {
        // Créer le membre d'équipe
        $member = TeamMember::create([
            'tenant_id' => $this->tenant_id,
            'user_id' => $user->id,
            'invited_by' => $this->invited_by,
            'role' => $this->role,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Mettre à jour l'invitation
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Associer l'utilisateur au tenant
        $user->update(['tenant_id' => $this->tenant_id]);

        return $member;
    }

    /**
     * Refuser l'invitation
     */
    public function decline(): void
    {
        $this->update(['status' => 'declined']);
    }

    /**
     * Marquer comme expirée
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Scope: Invitations en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '>', now());
    }

    /**
     * Obtenir le lien d'invitation
     */
    public function getInvitationUrlAttribute(): string
    {
        return url("/invitation/accept/{$this->token}");
    }

    /**
     * Obtenir le libellé du rôle
     */
    public function getRoleLabelAttribute(): string
    {
        return TeamMember::ROLES[$this->role]['label'] ?? $this->role;
    }
}
