<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111827; line-height: 1.6; }
        .container { padding: 40px; }
        
        .header { margin-bottom: 40px; }
        .header-table { display: table; width: 100%; }
        .header-left { display: table-cell; width: 60%; vertical-align: bottom; }
        .header-right { display: table-cell; width: 40%; vertical-align: bottom; text-align: right; }
        .header h1 { color: #111827; font-size: 20px; font-weight: 300; letter-spacing: 4px; text-transform: uppercase; }
        .header-line { width: 40px; height: 2px; background: #111827; margin-top: 8px; }
        .invoice-number { font-size: 12px; color: #6B7280; font-weight: 400; }
        
        .status-badge { display: inline-block; padding: 3px 10px; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; border: 1px solid; }
        .status-paid { border-color: #166534; color: #166534; }
        .status-pending { border-color: #92400E; color: #92400E; }
        .status-overdue { border-color: #991B1B; color: #991B1B; }
        .status-draft { border-color: #6B7280; color: #6B7280; }
        .status-sent { border-color: #1E40AF; color: #1E40AF; }
        
        .info-section { display: table; width: 100%; margin-bottom: 30px; }
        .info-column { display: table-cell; width: 50%; vertical-align: top; }
        .info-block { margin-bottom: 15px; }
        .info-block h3 { font-size: 8px; color: #9CA3AF; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; font-weight: 600; }
        .info-block p { margin: 2px 0; font-size: 10px; color: #374151; }
        .info-block .name { font-size: 12px; font-weight: 600; color: #111827; margin-bottom: 3px; }
        
        .separator { height: 1px; background: #E5E7EB; margin: 5px 0 25px 0; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { padding: 8px 0; text-align: left; font-weight: 400; font-size: 8px; text-transform: uppercase; letter-spacing: 2px; color: #9CA3AF; border-bottom: 1px solid #E5E7EB; }
        .items-table td { padding: 12px 0; border-bottom: 1px solid #F3F4F6; font-size: 10px; }
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        .items-table th.text-center, .items-table td.text-center { text-align: center; }
        
        .totals { width: 220px; margin-left: auto; margin-bottom: 30px; }
        .totals-row { display: table; width: 100%; margin-bottom: 6px; }
        .totals-row .label { display: table-cell; text-align: left; font-size: 10px; color: #9CA3AF; }
        .totals-row .value { display: table-cell; text-align: right; font-size: 10px; color: #374151; }
        .totals-row.total { padding-top: 10px; border-top: 1px solid #111827; margin-top: 10px; }
        .totals-row.total .label { font-size: 11px; font-weight: 600; color: #111827; }
        .totals-row.total .value { font-size: 14px; font-weight: 700; color: #111827; }
        
        .notes { margin-top: 25px; padding: 12px 0; border-top: 1px solid #E5E7EB; font-size: 9px; }
        .notes h4 { font-size: 8px; margin-bottom: 5px; color: #9CA3AF; text-transform: uppercase; letter-spacing: 2px; }
        .notes p { color: #6B7280; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 8px; color: #D1D5DB; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-table">
                <div class="header-left">
                    <h1>Facture</h1>
                    <div class="header-line"></div>
                </div>
                <div class="header-right">
                    <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                    <div style="margin-top: 8px;">
                        <span class="status-badge status-{{ $invoice->status }}">
                            @switch($invoice->status)
                                @case('paid') Payée @break
                                @case('pending') En attente @break
                                @case('overdue') En retard @break
                                @case('sent') Envoyée @break
                                @default Brouillon
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-column">
                <div class="info-block">
                    <h3>De</h3>
                    <p class="name">{{ $invoice->user->company_name ?? $invoice->user->name ?? config('app.name') }}</p>
                    <p>{{ $invoice->user->email ?? '' }}</p>
                    <p>{{ $invoice->user->phone ?? '' }}</p>
                    @if($invoice->user->address ?? false)<p>{{ $invoice->user->address }}</p>@endif
                </div>
            </div>
            <div class="info-column">
                <div class="info-block">
                    <h3>Facturé à</h3>
                    <p class="name">{{ $invoice->client->name }}</p>
                    <p>{{ $invoice->client->email }}</p>
                    @if($invoice->client->phone)<p>{{ $invoice->client->phone }}</p>@endif
                    @if($invoice->client->address)<p>{{ $invoice->client->address }}</p>@endif
                </div>
                <div class="info-block">
                    <h3>Dates</h3>
                    <p>Émission : {{ $invoice->issue_date->format('d/m/Y') }}</p>
                    <p>Échéance : {{ $invoice->due_date->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        
        <div class="separator"></div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-center" style="width: 10%;">Qté</th>
                    <th class="text-right" style="width: 15%;">Prix unit.</th>
                    <th class="text-right" style="width: 10%;">TVA</th>
                    <th class="text-right" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} XOF</td>
                    <td class="text-right">{{ number_format($item->tax_rate, 0) }}%</td>
                    <td class="text-right">{{ number_format($item->total, 0, ',', ' ') }} XOF</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totals">
            <div class="totals-row"><div class="label">Sous-total</div><div class="value">{{ number_format($invoice->subtotal, 0, ',', ' ') }} XOF</div></div>
            @if($invoice->tax_amount > 0)<div class="totals-row"><div class="label">TVA</div><div class="value">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} XOF</div></div>@endif
            @if($invoice->discount_amount > 0)<div class="totals-row"><div class="label">Remise</div><div class="value">-{{ number_format($invoice->discount_amount, 0, ',', ' ') }} XOF</div></div>@endif
            <div class="totals-row total"><div class="label">Total</div><div class="value">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</div></div>
        </div>
        
        @if($invoice->notes)
        <div class="notes"><h4>Notes</h4><p>{{ $invoice->notes }}</p></div>
        @endif
        
        <div class="footer">
            {{ $invoice->user->company_name ?? config('app.name') }}
        </div>
    </div>
</body>
</html>
