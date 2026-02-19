<?php

namespace App\Domain\Invoice\Models;

use App\Domain\Client\Models\Client;
use App\Domain\Tenant\Models\Tenant;
use App\Infrastructure\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CreditNote extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'user_id',
        'client_id',
        'invoice_id',
        'number',
        'status',
        'reason',
        'subtotal',
        'tax',
        'total',
        'currency',
        'issued_at',
        'notes',
        'items',
        'metadata',
    ];

    protected $casts = [
        'items' => 'array',
        'metadata' => 'array',
        'issued_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUSES = [
        'draft' => 'Brouillon',
        'issued' => 'Émis',
        'applied' => 'Appliqué',
        'cancelled' => 'Annulé',
    ];

    public const REASONS = [
        'error' => 'Erreur de facturation',
        'return' => 'Retour de marchandise',
        'discount' => 'Remise commerciale',
        'cancellation' => 'Annulation de commande',
        'duplicate' => 'Facture en double',
        'other' => 'Autre',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($creditNote) {
            if (empty($creditNote->uuid)) {
                $creditNote->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relation: appartient à un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation: appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Relation: appartient à un client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation: associé à une facture (optionnel)
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Scope: uniquement les avoirs émis
     */
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    /**
     * Scope: uniquement les brouillons
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
