<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Générer le numéro de facture
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = 'INV-' . date('Y') . '-' . str_pad(
                \App\Models\Invoice::count() + 1,
                5,
                '0',
                STR_PAD_LEFT
            );
        }

        // Générer l'UUID
        $data['uuid'] = \Illuminate\Support\Str::uuid();

        $invoice = static::getModel()::create($data);

        // Calculer les totaux
        if (class_exists(\App\Services\InvoiceCalculatorService::class)) {
            $calculator = app(\App\Services\InvoiceCalculatorService::class);
            $calculator->calculate($invoice);
        }

        return $invoice;
    }
}
