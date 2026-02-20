<?php

namespace App\Policies;

use App\Domain\Invoice\Models\RecurringInvoice;
use App\Models\User;

class RecurringInvoicePolicy
{
    /**
     * Determine whether the user can view any recurring invoices.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the recurring invoice.
     */
    public function view(User $user, RecurringInvoice $recurringInvoice): bool
    {
        return $user->tenant_id === $recurringInvoice->tenant_id;
    }

    /**
     * Determine whether the user can create recurring invoices.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the recurring invoice.
     */
    public function update(User $user, RecurringInvoice $recurringInvoice): bool
    {
        return $user->tenant_id === $recurringInvoice->tenant_id;
    }

    /**
     * Determine whether the user can delete the recurring invoice.
     */
    public function delete(User $user, RecurringInvoice $recurringInvoice): bool
    {
        return $user->tenant_id === $recurringInvoice->tenant_id
            && ! $recurringInvoice->is_active;
    }
}
