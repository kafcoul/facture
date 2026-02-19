<?php

namespace App\Filament\Resources\CreditNoteResource\Pages;

use App\Filament\Resources\CreditNoteResource;
use App\Models\CreditNote;
use Filament\Resources\Pages\CreateRecord;

class CreateCreditNote extends CreateRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['user_id'] = auth()->id();

        if (empty($data['number'])) {
            $prefix = 'AV';
            $last = CreditNote::where('tenant_id', auth()->user()->tenant_id)
                ->orderByDesc('id')
                ->first();
            $nextNum = $last ? ((int) preg_replace('/[^0-9]/', '', $last->number) + 1) : 1;
            $data['number'] = $prefix . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
