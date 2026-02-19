<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a2e; line-height: 1.6; background: #fff; }
        
        .page { position: relative; min-height: 100%; }
        
        /* Design géométrique moderne */
        .geometric-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            overflow: hidden;
        }
        
        .geometric-shape {
            position: absolute;
            border-radius: 50%;
        }
        
        .shape-1 { width: 200px; height: 200px; background: rgba(16, 185, 129, 0.1); top: -50px; right: -50px; }
        .shape-2 { width: 150px; height: 150px; background: rgba(6, 182, 212, 0.1); top: 80px; right: 30px; }
        .shape-3 { width: 100px; height: 100px; background: rgba(139, 92, 246, 0.1); top: 20px; right: 120px; }
        
        .container { padding: 40px; position: relative; z-index: 1; }
        
        /* Header Premium */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        
        .brand {
            display: table;
            width: 100%;
        }
        
        .brand-logo {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }
        
        .brand-logo-box {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 50%, #8b5cf6 100%);
            border-radius: 16px;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            line-height: 60px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        .brand-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }
        
        .brand-name {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a2e;
            letter-spacing: -0.5px;
        }
        
        .brand-tagline {
            font-size: 10px;
            color: #10b981;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .invoice-meta {
            text-align: right;
        }
        
        .invoice-type {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .status-pill {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 12px;
        }
        
        .status-paid { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
        .status-pending { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); }
        .status-overdue { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4); }
        .status-draft { background: #e5e7eb; color: #6b7280; }
        .status-sent { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); }
        
        /* Cards Section */
        .cards-section {
            display: table;
            width: 100%;
            margin-bottom: 35px;
        }
        
        .card {
            display: table-cell;
            width: 31%;
            vertical-align: top;
            background: #fff;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }
        
        .card-spacer { display: table-cell; width: 3.5%; }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 12px;
        }
        
        .card-icon.from { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .card-icon.to { background: linear-gradient(135deg, #06b6d4, #0891b2); color: #fff; }
        .card-icon.date { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; }
        
        .card-label {
            font-size: 9px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        
        .card-text {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.5;
        }
        
        .date-grid { display: table; width: 100%; margin-top: 10px; }
        .date-item { display: table-cell; width: 50%; }
        .date-label { font-size: 8px; color: #9ca3af; text-transform: uppercase; }
        .date-value { font-size: 12px; font-weight: 700; color: #1a1a2e; margin-top: 2px; }
        
        /* Items Table Premium */
        .items-section { margin-bottom: 30px; }
        
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            border-radius: 2px;
            margin-right: 10px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        
        .items-table thead {
            background: linear-gradient(135deg, #1a1a2e, #2d2d44);
        }
        
        .items-table th {
            padding: 16px 14px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
        }
        
        .items-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 11px;
            background: #fff;
        }
        
        .items-table tbody tr:hover td { background: #f8fafc; }
        .items-table tbody tr:last-child td { border-bottom: none; }
        
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        .items-table th.text-center, .items-table td.text-center { text-align: center; }
        
        .item-main { font-weight: 700; color: #1a1a2e; font-size: 12px; }
        .item-sub { font-size: 9px; color: #9ca3af; margin-top: 3px; }
        
        .item-total {
            font-weight: 800;
            color: #10b981;
            font-size: 12px;
        }
        
        /* Bottom Section */
        .bottom-section {
            display: table;
            width: 100%;
        }
        
        .left-section { display: table-cell; width: 55%; vertical-align: top; padding-right: 25px; }
        .right-section { display: table-cell; width: 45%; vertical-align: top; }
        
        /* Payment Box Premium */
        .payment-box {
            background: linear-gradient(135deg, #1a1a2e, #2d2d44);
            border-radius: 20px;
            padding: 25px;
            color: #fff;
        }
        
        .payment-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .payment-title {
            display: table-cell;
            vertical-align: middle;
        }
        
        .payment-title h4 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 3px;
        }
        
        .payment-title p {
            font-size: 10px;
            color: #9ca3af;
        }
        
        .qr-container {
            display: table-cell;
            width: 100px;
            text-align: right;
        }
        
        .qr-box {
            width: 90px;
            height: 90px;
            background: #fff;
            border-radius: 12px;
            padding: 6px;
            display: inline-block;
        }
        
        .qr-box img {
            width: 100%;
            height: 100%;
            border-radius: 6px;
        }
        
        .payment-methods {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 15px;
        }
        
        .payment-method {
            display: table;
            width: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 10px;
        }
        
        .payment-method:last-child { margin-bottom: 0; }
        
        .pm-icon {
            display: table-cell;
            width: 40px;
            vertical-align: middle;
        }
        
        .pm-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            text-align: center;
            line-height: 32px;
            font-size: 16px;
        }
        
        .pm-icon-box.wave { background: #1dc0f2; }
        .pm-icon-box.orange { background: #ff7900; }
        .pm-icon-box.momo { background: #ffcc00; }
        .pm-icon-box.moov { background: #0066b3; }
        
        .pm-details {
            display: table-cell;
            vertical-align: middle;
        }
        
        .pm-name {
            font-size: 10px;
            color: #9ca3af;
            margin-bottom: 2px;
        }
        
        .pm-number {
            font-size: 14px;
            font-weight: 700;
            font-family: monospace;
            letter-spacing: 1px;
        }
        
        /* Totals Premium */
        .totals-box {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }
        
        .totals-row {
            display: table;
            width: 100%;
            padding: 10px 0;
        }
        
        .totals-row .label {
            display: table-cell;
            font-size: 12px;
            color: #6b7280;
        }
        
        .totals-row .value {
            display: table-cell;
            text-align: right;
            font-size: 12px;
            color: #1a1a2e;
            font-weight: 600;
        }
        
        .totals-row.discount .value { color: #ef4444; }
        
        .totals-divider {
            height: 2px;
            background: linear-gradient(90deg, #10b981, #06b6d4, #8b5cf6);
            border-radius: 1px;
            margin: 15px 0;
        }
        
        .totals-row.grand-total {
            padding: 15px 0 0 0;
        }
        
        .totals-row.grand-total .label {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
        }
        
        .totals-row.grand-total .value {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Amount in words */
        .amount-words {
            margin-top: 15px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 10px;
            text-align: center;
        }
        
        .amount-words p {
            font-size: 10px;
            color: #6b7280;
            font-style: italic;
        }
        
        /* Notes */
        .notes-section {
            margin-top: 25px;
        }
        
        .notes-box {
            background: linear-gradient(135deg, #eff6ff, #f0fdf4);
            border-radius: 16px;
            padding: 20px;
            border-left: 4px solid #10b981;
        }
        
        .notes-box h4 {
            font-size: 11px;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .notes-box p {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }
        
        /* Footer Premium */
        .footer {
            margin-top: 40px;
            text-align: center;
        }
        
        .footer-content {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            border-radius: 20px;
            padding: 25px;
            color: #fff;
        }
        
        .footer-thanks {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .footer-company {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .footer-contact {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 8px;
        }
        
        .footer-badge {
            display: inline-block;
            margin-top: 15px;
            padding: 6px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="geometric-bg">
            <div class="geometric-shape shape-1"></div>
            <div class="geometric-shape shape-2"></div>
            <div class="geometric-shape shape-3"></div>
        </div>
        
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="header-left">
                    <div class="brand">
                        <div class="brand-logo">
                            <div class="brand-logo-box">
                                {{ strtoupper(substr($invoice->user->company_name ?? $invoice->user->name ?? 'E', 0, 2)) }}
                            </div>
                        </div>
                        <div class="brand-info">
                            <div class="brand-name">{{ $invoice->user->company_name ?? $invoice->user->name ?? config('app.name') }}</div>
                            <div class="brand-tagline">Enterprise Solution</div>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="invoice-meta">
                        <div class="invoice-type">Facture</div>
                        <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                        <span class="status-pill status-{{ $invoice->status }}">
                            @switch($invoice->status)
                                @case('paid') ✓ Payée @break
                                @case('pending') ⏳ En attente @break
                                @case('overdue') ⚠ En retard @break
                                @case('sent') ✉ Envoyée @break
                                @default 📝 Brouillon
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Info Cards -->
            <div class="cards-section">
                <div class="card">
                    <div class="card-icon from">📤</div>
                    <div class="card-label">Émetteur</div>
                    <div class="card-title">{{ $invoice->user->company_name ?? $invoice->user->name ?? config('app.name') }}</div>
                    <div class="card-text">
                        {{ $invoice->user->email ?? '' }}<br>
                        {{ $invoice->user->phone ?? '' }}<br>
                        {{ $invoice->user->address ?? '' }}
                    </div>
                </div>
                <div class="card-spacer"></div>
                <div class="card">
                    <div class="card-icon to">📥</div>
                    <div class="card-label">Facturé à</div>
                    <div class="card-title">{{ $invoice->client->name }}</div>
                    <div class="card-text">
                        {{ $invoice->client->email }}<br>
                        @if($invoice->client->phone){{ $invoice->client->phone }}<br>@endif
                        {{ $invoice->client->address ?? '' }}
                    </div>
                </div>
                <div class="card-spacer"></div>
                <div class="card">
                    <div class="card-icon date">📅</div>
                    <div class="card-label">Dates</div>
                    <div class="date-grid">
                        <div class="date-item">
                            <div class="date-label">Émission</div>
                            <div class="date-value">{{ $invoice->issue_date->format('d/m/Y') }}</div>
                        </div>
                        <div class="date-item">
                            <div class="date-label">Échéance</div>
                            <div class="date-value">{{ $invoice->due_date->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Items -->
            <div class="items-section">
                <div class="section-title">Détail des prestations</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 42%;">Description</th>
                            <th class="text-center" style="width: 12%;">Quantité</th>
                            <th class="text-right" style="width: 18%;">Prix unitaire</th>
                            <th class="text-right" style="width: 12%;">TVA</th>
                            <th class="text-right" style="width: 16%;">Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <div class="item-main">{{ $item->description }}</div>
                                @if($item->product && $item->product->sku)
                                <div class="item-sub">Référence: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} XOF</td>
                            <td class="text-right">{{ number_format($item->tax_rate, 0) }}%</td>
                            <td class="text-right"><span class="item-total">{{ number_format($item->total, 0, ',', ' ') }} XOF</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Bottom Section -->
            <div class="bottom-section">
                <div class="left-section">
                    @if(($invoice->user->wave_number ?? false) || ($invoice->user->orange_money_number ?? false) || ($invoice->user->momo_number ?? false) || ($invoice->user->moov_money_number ?? false))
                    <div class="payment-box">
                        <div class="payment-header">
                            <div class="payment-title">
                                <h4>💳 Paiement Mobile</h4>
                                <p>Scannez le QR ou utilisez les numéros</p>
                            </div>
                            <div class="qr-container">
                                <div class="qr-box">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=78x78&data={{ urlencode(route('invoices.public', $invoice->uuid)) }}&color=1a1a2e" alt="QR">
                                </div>
                            </div>
                        </div>
                        <div class="payment-methods">
                            @if($invoice->user->wave_number ?? false)
                            <div class="payment-method">
                                <div class="pm-icon"><div class="pm-icon-box wave">🌊</div></div>
                                <div class="pm-details">
                                    <div class="pm-name">Wave</div>
                                    <div class="pm-number">{{ $invoice->user->wave_number }}</div>
                                </div>
                            </div>
                            @endif
                            @if($invoice->user->orange_money_number ?? false)
                            <div class="payment-method">
                                <div class="pm-icon"><div class="pm-icon-box orange">🍊</div></div>
                                <div class="pm-details">
                                    <div class="pm-name">Orange Money</div>
                                    <div class="pm-number">{{ $invoice->user->orange_money_number }}</div>
                                </div>
                            </div>
                            @endif
                            @if($invoice->user->momo_number ?? false)
                            <div class="payment-method">
                                <div class="pm-icon"><div class="pm-icon-box momo">📱</div></div>
                                <div class="pm-details">
                                    <div class="pm-name">MTN Mobile Money</div>
                                    <div class="pm-number">{{ $invoice->user->momo_number }}</div>
                                </div>
                            </div>
                            @endif
                            @if($invoice->user->moov_money_number ?? false)
                            <div class="payment-method">
                                <div class="pm-icon"><div class="pm-icon-box moov">📲</div></div>
                                <div class="pm-details">
                                    <div class="pm-name">Moov Money</div>
                                    <div class="pm-number">{{ $invoice->user->moov_money_number }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="right-section">
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
                        <div class="totals-row discount">
                            <div class="label">Remise accordée</div>
                            <div class="value">- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} XOF</div>
                        </div>
                        @endif
                        <div class="totals-divider"></div>
                        <div class="totals-row grand-total">
                            <div class="label">Total TTC</div>
                            <div class="value">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</div>
                        </div>
                        <div class="amount-words">
                            <p>Arrêté la présente facture à la somme de :<br><strong>{{ \App\Helpers\NumberToWords::convert($invoice->total) }} Francs CFA</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($invoice->notes)
            <div class="notes-section">
                <div class="notes-box">
                    <h4>📝 Notes & Conditions</h4>
                    <p>{{ $invoice->notes }}</p>
                </div>
            </div>
            @endif
            
            <div class="footer">
                <div class="footer-content">
                    <div class="footer-thanks">Merci pour votre confiance ! 🙏</div>
                    <div class="footer-company">{{ $invoice->user->company_name ?? config('app.name') }}</div>
                    <div class="footer-contact">
                        {{ $invoice->user->email ?? '' }}
                        @if($invoice->user->phone ?? false) • {{ $invoice->user->phone }} @endif
                    </div>
                    <div class="footer-badge">🏆 Enterprise Plan</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
