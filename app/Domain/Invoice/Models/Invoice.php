<?php

namespace App\Domain\Invoice\Models;

use App\Domain\Client\Models\Client;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\Payment\Models\Payment;
use App\Infrastructure\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'client_id',
        'number',
        'uuid',
        'type',
        'status',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'issued_at',
        'due_date',
        'paid_at',
        'pdf_path',
        'public_hash',
        'notes',
        'terms',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'issued_at' => 'datetime',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->uuid)) {
                $invoice->uuid = (string) Str::uuid();
            }
            if (empty($invoice->public_hash)) {
                $invoice->public_hash = Str::random(32);
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\InvoiceFactory::new();
    }

    /**
     * Relation: Une facture appartient à un tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relation: Une facture appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Relation: Une facture appartient à un client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation: Une facture a plusieurs items
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Relation: Une facture a plusieurs paiements
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope: Factures impayées
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'viewed', 'overdue']);
    }

    /**
     * Scope: Factures payées
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope: Factures overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                     ->where('due_date', '<', now());
    }

    /**
     * Vérifier si la facture est payée
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Vérifier si la facture est overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'overdue' && $this->due_date < now();
    }

    /**
     * Marquer comme payée
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Calculer le montant restant à payer
     */
    public function getRemainingAmount(): float
    {
        $totalPaid = $this->payments()->where('status', 'completed')->sum('amount');
        return max(0, $this->total - $totalPaid);
    }

    /**
     * Générer l'URL publique
     */
    public function getPublicUrl(): string
    {
        return route('invoice.public', ['hash' => $this->public_hash]);
    }
}
