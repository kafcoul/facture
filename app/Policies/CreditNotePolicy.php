<?php

namespace App\Policies;

use App\Domain\Invoice\Models\CreditNote;
use App\Models\User;

class CreditNotePolicy
{
    /**
     * Determine whether the user can view any credit notes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the credit note.
     */
    public function view(User $user, CreditNote $creditNote): bool
    {
        return $user->tenant_id === $creditNote->tenant_id;
    }

    /**
     * Determine whether the user can create credit notes.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the credit note.
     */
    public function update(User $user, CreditNote $creditNote): bool
    {
        return $user->tenant_id === $creditNote->tenant_id
            && in_array($creditNote->status, ['draft']);
    }

    /**
     * Determine whether the user can delete the credit note.
     */
    public function delete(User $user, CreditNote $creditNote): bool
    {
        return $user->tenant_id === $creditNote->tenant_id
            && $creditNote->status === 'draft';
    }
}
