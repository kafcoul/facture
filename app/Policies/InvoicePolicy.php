<?php

namespace App\Policies;

use App\Domain\Invoice\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->tenant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        // On ne peut modifier que les factures brouillon ou envoyées (pas payées/annulées)
        return $user->tenant_id === $invoice->tenant_id
            && in_array($invoice->status, ['draft', 'sent', 'viewed', 'overdue']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        // On ne peut supprimer que les brouillons
        return $user->tenant_id === $invoice->tenant_id
            && $invoice->status === 'draft';
    }

    /**
     * Determine whether the user can send the invoice.
     */
    public function send(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->tenant_id
            && in_array($invoice->status, ['draft', 'sent', 'viewed', 'overdue']);
    }

    /**
     * Determine whether the user can duplicate the invoice.
     */
    public function duplicate(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->tenant_id;
    }

    /**
     * Determine whether the user can change the invoice status.
     */
    public function changeStatus(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->tenant_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->tenant_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->tenant_id === $invoice->tenant_id && $user->isAdmin();
    }
}
