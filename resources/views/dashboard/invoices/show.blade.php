@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec retour et actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('client.invoices.index') }}" 
                class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Facture {{ $invoice->number }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Créée le {{ $invoice->created_at?->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <div class="flex gap-2">
            @if($invoice->pdf_path)
            <a href="{{ route('client.invoices.download', $invoice) }}" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Télécharger PDF
            </a>
            @endif
        </div>
    </div>

    <!-- Statut de la facture -->
    <div class="bg-white rounded-lg shadow p-6">
        @php
            $statusConfig = [
                'draft' => ['label' => 'Brouillon', 'class' => 'bg-gray-100 text-gray-800', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                'sent' => ['label' => 'Envoyée', 'class' => 'bg-blue-100 text-blue-800', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                'viewed' => ['label' => 'Vue par le client', 'class' => 'bg-purple-100 text-purple-800', 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                'paid' => ['label' => 'Payée', 'class' => 'bg-green-100 text-green-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                'overdue' => ['label' => 'En retard', 'class' => 'bg-red-100 text-red-800', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'cancelled' => ['label' => 'Annulée', 'class' => 'bg-gray-100 text-gray-600', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
            $status = $statusConfig[$invoice->status] ?? ['label' => $invoice->status, 'class' => 'bg-gray-100 text-gray-800', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
        @endphp
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 {{ str_replace('bg-', 'text-', explode(' ', $status['class'])[0]) }}" fill="currentColor" viewBox="0 0 24 24">
                    <path d="{{ $status['icon'] }}"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Statut de la facture</h3>
                <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations du client -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du client</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nom</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $invoice->client->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $invoice->client->email }}</p>
                    </div>
                    @if($invoice->client->phone)
                    <div>
                        <p class="text-sm font-medium text-gray-500">Téléphone</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $invoice->client->phone }}</p>
                    </div>
                    @endif
                    @if($invoice->client->address)
                    <div class="col-span-2">
                        <p class="text-sm font-medium text-gray-500">Adresse</p>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $invoice->client->address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Articles de la facture -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Articles</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantité
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix unitaire
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item->description }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-center">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    {{ number_format($item->unit_price, 2, ',', ' ') }} €
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                    {{ number_format($item->total, 2, ',', ' ') }} €
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Récapitulatif -->
        <div class="space-y-6">
            <!-- Dates importantes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Dates importantes</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de la facture</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $invoice->created_at?->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date d'échéance</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $invoice->due_date->format('d/m/Y') }}</dd>
                    </div>
                    @if($invoice->paid_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de paiement</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $invoice->paid_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Totaux -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Sous-total</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($invoice->subtotal, 2, ',', ' ') }} €</dd>
                    </div>
                    @if($invoice->tax_amount > 0)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">TVA ({{ $invoice->tax_rate }}%)</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($invoice->tax_amount, 2, ',', ' ') }} €</dd>
                    </div>
                    @endif
                    @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-green-600">
                        <dt class="text-sm">Remise</dt>
                        <dd class="text-sm font-medium">-{{ number_format($invoice->discount_amount, 2, ',', ' ') }} €</dd>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <dt class="text-base font-medium text-gray-900">Total</dt>
                        <dd class="text-base font-bold text-gray-900">{{ number_format($invoice->total, 2, ',', ' ') }} €</dd>
                    </div>
                </dl>
            </div>

            <!-- Historique des paiements -->
            @if($invoice->payments->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Paiements reçus</h3>
                <div class="space-y-3">
                    @foreach($invoice->payments as $payment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($payment->status === 'completed')
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ number_format($payment->amount, 2, ',', ' ') }} €
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $payment->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 uppercase">
                            {{ $payment->gateway }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
