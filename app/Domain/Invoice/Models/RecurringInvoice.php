<?php

namespace App\Domain\Invoice\Models;

use App\Domain\Client\Models\Client;
use App\Domain\Tenant\Models\Tenant;
use App\Infrastructure\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringInvoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'client_id',
        'frequency',
        'start_date',
        'end_date',
        'next_due_date',
        'occurrences_limit',
        'occurrences_count',
        'subtotal',
        'tax',
        'total',
        'currency',
        'notes',
        'terms',
        'is_active',
        'auto_send',
        'items',
        'metadata',
        'last_generated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
        'items' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'auto_send' => 'boolean',
        'last_generated_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const FREQUENCIES = [
        'weekly' => 'Hebdomadaire',
        'biweekly' => 'Bi-mensuel',
        'monthly' => 'Mensuel',
        'quarterly' => 'Trimestriel',
        'biannual' => 'Semestriel',
        'yearly' => 'Annuel',
    ];

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
     * Relation: les factures générées
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'recurring_invoice_id');
    }

    /**
     * Scope: Récurrences actives et à générer
     */
    public function scopeDue($query)
    {
        return $query->where('is_active', true)
            ->where('next_due_date', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('occurrences_limit')
                  ->orWhereColumn('occurrences_count', '<', 'occurrences_limit');
            });
    }

    /**
     * Scope: Récurrences actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculer la prochaine date d'échéance
     */
    public function calculateNextDueDate(): \Carbon\Carbon
    {
        $current = $this->next_due_date;

        return match ($this->frequency) {
            'weekly' => $current->addWeek(),
            'biweekly' => $current->addWeeks(2),
            'monthly' => $current->addMonth(),
            'quarterly' => $current->addMonths(3),
            'biannual' => $current->addMonths(6),
            'yearly' => $current->addYear(),
            default => $current->addMonth(),
        };
    }

    /**
     * Vérifier si la récurrence peut encore générer des factures
     */
    public function canGenerate(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        if ($this->occurrences_limit && $this->occurrences_count >= $this->occurrences_limit) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir le label de la fréquence
     */
    public function getFrequencyLabelAttribute(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? $this->frequency;
    }
}
