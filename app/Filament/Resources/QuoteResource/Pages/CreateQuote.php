<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['user_id'] = auth()->id();
        $data['type'] = 'quote';

        if (empty($data['number'])) {
            $prefix = 'DEV';
            $lastQuote = \App\Models\Invoice::where('type', 'quote')
                ->where('tenant_id', auth()->user()->tenant_id)
                ->orderByDesc('id')
                ->first();
            $nextNum = $lastQuote ? ((int) preg_replace('/[^0-9]/', '', $lastQuote->number) + 1) : 1;
            $data['number'] = $prefix . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
