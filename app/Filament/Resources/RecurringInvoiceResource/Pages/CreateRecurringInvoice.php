<?php

namespace App\Filament\Resources\RecurringInvoiceResource\Pages;

use App\Filament\Resources\RecurringInvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringInvoice extends CreateRecord
{
    protected static string $resource = RecurringInvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
