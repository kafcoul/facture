<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1f2937; line-height: 1.6; background: #fff; }
        
        .page { position: relative; min-height: 100%; }
        
        /* Bandeau coloré en haut */
        .top-banner {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            height: 120px;
            position: relative;
        }
        
        .top-banner::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 60px;
            background: #fff;
            border-radius: 30px 30px 0 0;
        }
        
        .container { padding: 0 40px 40px 40px; position: relative; z-index: 1; }
        
        /* Header avec logo et titre */
        .header {
            display: table;
            width: 100%;
            margin-top: -80px;
            margin-bottom: 30px;
        }
        
        .header-left { display: table-cell; width: 60%; vertical-align: bottom; }
        .header-right { display: table-cell; width: 40%; vertical-align: bottom; text-align: right; }
        
        .company-logo {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .company-logo-text {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            line-height: 80px;
        }
        
        .invoice-title {
            font-size: 32px;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: -1px;
        }
        
        .invoice-number-box {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        
        .status-paid { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
        .status-overdue { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
        .status-draft { background: #e5e7eb; color: #6b7280; }
        .status-sent { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; }
        
        /* Info cards */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .info-card {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
        }
        
        .info-card:first-child { margin-right: 4%; }
        
        .info-card h3 {
            font-size: 10px;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            font-weight: 700;
        }
        
        .info-card .name {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .info-card p {
            font-size: 11px;
            color: #6b7280;
            margin: 3px 0;
        }
        
        .dates-info {
            display: table;
            width: 100%;
            margin-top: 15px;
            background: #fff;
            border-radius: 10px;
            padding: 12px;
        }
        
        .date-item {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        
        .date-item .label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .date-item .value {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            margin-top: 3px;
        }
        
        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-radius: 16px;
            overflow: hidden;
        }
        
        .items-table thead {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        
        .items-table th {
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fff;
        }
        
        .items-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
        }
        
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }
        .items-table tbody tr:last-child td { border-bottom: none; }
        
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        .items-table th.text-center, .items-table td.text-center { text-align: center; }
        
        .item-name { font-weight: 600; color: #1f2937; }
        .item-desc { font-size: 10px; color: #9ca3af; margin-top: 2px; }
        
        /* Totals et QR Code */
        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .payment-section {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .totals-section {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }
        
        .payment-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 16px;
            padding: 20px;
            border: 2px solid #f59e0b;
        }
        
        .payment-box h4 {
            font-size: 12px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .payment-box h4::before {
            content: '📱';
            margin-right: 8px;
            font-size: 16px;
        }
        
        .mobile-payments {
            display: table;
            width: 100%;
        }
        
        .qr-section {
            display: table-cell;
            width: 100px;
            vertical-align: top;
        }
        
        .qr-code {
            width: 90px;
            height: 90px;
            background: #fff;
            border-radius: 10px;
            padding: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        
        .payment-details {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
        }
        
        .payment-method {
            background: #fff;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }
        
        .payment-method-icon {
            display: table-cell;
            width: 30px;
            vertical-align: middle;
        }
        
        .payment-method-icon img {
            width: 24px;
            height: 24px;
        }
        
        .payment-method-info {
            display: table-cell;
            vertical-align: middle;
        }
        
        .payment-method-name {
            font-size: 10px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .payment-method-number {
            font-size: 12px;
            font-weight: 600;
            color: #6366f1;
            font-family: monospace;
        }
        
        /* Totals */
        .totals-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
        }
        
        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .totals-row .label {
            display: table-cell;
            text-align: left;
            font-size: 11px;
            color: #6b7280;
        }
        
        .totals-row .value {
            display: table-cell;
            text-align: right;
            font-size: 11px;
            color: #1f2937;
            font-weight: 500;
        }
        
        .totals-row.total {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px dashed #e5e7eb;
        }
        
        .totals-row.total .label {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .totals-row.total .value {
            font-size: 20px;
            font-weight: 800;
            color: #6366f1;
        }
        
        /* Notes */
        .notes {
            margin-top: 25px;
            padding: 16px;
            background: #eff6ff;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
        }
        
        .notes h4 {
            font-size: 10px;
            font-weight: 700;
            color: #3b82f6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .notes p { font-size: 11px; color: #4b5563; }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            color: #fff;
        }
        
        .footer p { font-size: 11px; margin: 3px 0; }
        .footer .thanks { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="top-banner"></div>
        
        <div class="container">
            <div class="header">
                <div class="header-left">
                    <div class="company-logo-text">
                        {{ strtoupper(substr($invoice->user->company_name ?? $invoice->user->name ?? 'F', 0, 2)) }}
                    </div>
                    <div class="invoice-title">FACTURE</div>
                </div>
                <div class="header-right">
                    <div class="invoice-number-box">#{{ $invoice->invoice_number }}</div>
                    <br>
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
            
            <div class="info-section">
                <div class="info-card">
                    <h3>Émetteur</h3>
                    <div class="name">{{ $invoice->user->company_name ?? $invoice->user->name ?? config('app.name') }}</div>
                    <p>{{ $invoice->user->email ?? '' }}</p>
                    <p>{{ $invoice->user->phone ?? '' }}</p>
                    @if($invoice->user->address ?? false)<p>{{ $invoice->user->address }}</p>@endif
                </div>
                <div style="display: table-cell; width: 4%;"></div>
                <div class="info-card">
                    <h3>Client</h3>
                    <div class="name">{{ $invoice->client->name }}</div>
                    <p>{{ $invoice->client->email }}</p>
                    @if($invoice->client->phone)<p>📞 {{ $invoice->client->phone }}</p>@endif
                    @if($invoice->client->address)<p>📍 {{ $invoice->client->address }}</p>@endif
                    
                    <div class="dates-info">
                        <div class="date-item">
                            <div class="label">Date émission</div>
                            <div class="value">{{ $invoice->issue_date->format('d/m/Y') }}</div>
                        </div>
                        <div class="date-item">
                            <div class="label">Échéance</div>
                            <div class="value">{{ $invoice->due_date->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Description</th>
                        <th class="text-center" style="width: 12%;">Qté</th>
                        <th class="text-right" style="width: 18%;">Prix unitaire</th>
                        <th class="text-right" style="width: 10%;">TVA</th>
                        <th class="text-right" style="width: 15%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->description }}</div>
                            @if($item->product && $item->product->sku)
                            <div class="item-desc">Réf: {{ $item->product->sku }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} XOF</td>
                        <td class="text-right">{{ number_format($item->tax_rate, 0) }}%</td>
                        <td class="text-right"><strong>{{ number_format($item->total, 0, ',', ' ') }} XOF</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="bottom-section">
                <div class="payment-section">
                    @if(($invoice->user->wave_number ?? false) || ($invoice->user->orange_money_number ?? false) || ($invoice->user->momo_number ?? false))
                    <div class="payment-box">
                        <h4>Paiement Mobile Money</h4>
                        <div class="mobile-payments">
                            <div class="qr-section">
                                <div class="qr-code">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode(route('invoices.public', $invoice->uuid)) }}" alt="QR Code">
                                </div>
                                <p style="font-size: 8px; text-align: center; margin-top: 5px; color: #92400e;">Scanner pour payer</p>
                            </div>
                            <div class="payment-details">
                                @if($invoice->user->wave_number ?? false)
                                <div class="payment-method">
                                    <div class="payment-method-icon">🌊</div>
                                    <div class="payment-method-info">
                                        <div class="payment-method-name">Wave</div>
                                        <div class="payment-method-number">{{ $invoice->user->wave_number }}</div>
                                    </div>
                                </div>
                                @endif
                                @if($invoice->user->orange_money_number ?? false)
                                <div class="payment-method">
                                    <div class="payment-method-icon">🍊</div>
                                    <div class="payment-method-info">
                                        <div class="payment-method-name">Orange Money</div>
                                        <div class="payment-method-number">{{ $invoice->user->orange_money_number }}</div>
                                    </div>
                                </div>
                                @endif
                                @if($invoice->user->momo_number ?? false)
                                <div class="payment-method">
                                    <div class="payment-method-icon">📱</div>
                                    <div class="payment-method-info">
                                        <div class="payment-method-name">MTN MoMo</div>
                                        <div class="payment-method-number">{{ $invoice->user->momo_number }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="totals-section">
                    <div class="totals-box">
                        <div class="totals-row">
                            <div class="label">Sous-total HT</div>
                            <div class="value">{{ number_format($invoice->subtotal, 0, ',', ' ') }} XOF</div>
                        </div>
                        @if($invoice->tax_amount > 0)
                        <div class="totals-row">
                            <div class="label">TVA (18%)</div>
                            <div class="value">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} XOF</div>
                        </div>
                        @endif
                        @if($invoice->discount_amount > 0)
                        <div class="totals-row">
                            <div class="label">Remise</div>
                            <div class="value" style="color: #ef4444;">-{{ number_format($invoice->discount_amount, 0, ',', ' ') }} XOF</div>
                        </div>
                        @endif
                        <div class="totals-row total">
                            <div class="label">TOTAL TTC</div>
                            <div class="value">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($invoice->notes)
            <div class="notes">
                <h4>📝 Notes</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif
            
            <div class="footer">
                <p class="thanks">Merci pour votre confiance ! 🙏</p>
                <p>{{ $invoice->user->company_name ?? config('app.name') }}</p>
                <p>{{ $invoice->user->email ?? '' }} {{ $invoice->user->phone ? '• ' . $invoice->user->phone : '' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
