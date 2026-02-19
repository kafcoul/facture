<div>
    @if ($this->getOverdueCount() > 0)
        <div class="rounded-xl bg-danger-50 dark:bg-danger-950 p-4 ring-1 ring-danger-200 dark:ring-danger-800">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-danger-500" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-danger-800 dark:text-danger-200">
                        ⚠️ {{ $this->getOverdueCount() }} facture(s) en retard
                    </h3>
                    <p class="mt-1 text-sm text-danger-700 dark:text-danger-300">
                        Montant total impayé : <strong>{{ number_format($this->getOverdueTotal(), 0, ',', ' ') }}
                            XOF</strong>.
                        Pensez à relancer vos clients.
                    </p>
                </div>
                <a href="{{ \App\Filament\Resources\InvoiceResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'overdue']]]) }}"
                    class="rounded-lg bg-danger-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-danger-500 transition-colors">
                    Voir les retards
                </a>
            </div>
        </div>
    @endif
</div>
