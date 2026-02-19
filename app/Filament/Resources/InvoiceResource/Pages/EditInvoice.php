<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view_pdf')
                ->label('Voir PDF')
                ->icon('heroicon-o-document')
                ->url(fn () => route('invoices.download', $this->record->uuid))
                ->openUrlInNewTab(),
            Actions\Action::make('view_public')
                ->label('Voir page publique')
                ->icon('heroicon-o-link')
                ->url(fn () => route('invoices.public', $this->record->uuid))
                ->openUrlInNewTab(),
        ];
    }

    protected function afterSave(): void
    {
        // Recalculer les totaux après modification
        if (class_exists(\App\Services\InvoiceCalculatorService::class)) {
            $calculator = app(\App\Services\InvoiceCalculatorService::class);
            $calculator->calculate($this->record);
        }
    }
}
