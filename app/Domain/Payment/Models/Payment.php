<?php

namespace App\Domain\Payment\Models;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Tenant\Models\Tenant;
use App\Infrastructure\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'user_id',
        'amount',
        'gateway',
        'transaction_id',
        'status',
        'currency',
        'payment_method',
        'metadata',
        'completed_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    /**
     * Relation: Un paiement appartient à un tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation: Un paiement appartient à une facture
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relation: Un paiement appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Scope: Paiements complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Paiements en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Paiements échoués
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Marquer comme complété
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Marquer comme échoué
     */
    public function markAsFailed(?string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Vérifier si le paiement est complété
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
