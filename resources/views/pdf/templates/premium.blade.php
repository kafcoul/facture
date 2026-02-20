<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #E5E7EB;
            line-height: 1.6;
            background: #1F2937;
        }

        .page {
            position: relative;
            min-height: 100%;
            background: #1F2937;
        }

        .gold-accent {
            height: 4px;
            background: linear-gradient(90deg, #D97706, #F59E0B, #D97706);
        }

        .container {
            padding: 40px;
            position: relative;
            z-index: 1;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }

        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

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
            background: linear-gradient(135deg, #D97706, #F59E0B);
            border-radius: 16px;
            color: #1F2937;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            line-height: 60px;
        }

        .brand-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            color: #F9FAFB;
            letter-spacing: -0.5px;
        }

        .brand-tagline {
            font-size: 10px;
            color: #D97706;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-type {
            font-size: 10px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 28px;
            font-weight: 800;
            color: #D97706;
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

        .status-paid {
            background: #059669;
            color: #fff;
        }

        .status-pending {
            background: #D97706;
            color: #fff;
        }

        .status-overdue {
            background: #DC2626;
            color: #fff;
        }

        .status-draft {
            background: #374151;
            color: #9CA3AF;
        }

        .status-sent {
            background: #D97706;
            color: #1F2937;
        }

        .cards-section {
            display: table;
            width: 100%;
            margin-bottom: 35px;
        }

        .card {
            display: table-cell;
            width: 31%;
            vertical-align: top;
            background: #111827;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #374151;
        }

        .card-spacer {
            display: table-cell;
            width: 3.5%;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            text-align: center;
            line-height: 40px;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .card-icon.from {
            background: linear-gradient(135deg, #D97706, #B45309);
            color: #fff;
        }

        .card-icon.to {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            color: #1F2937;
        }

        .card-icon.date {
            background: linear-gradient(135deg, #374151, #4B5563);
            color: #D97706;
        }

        .card-label {
            font-size: 9px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: #F9FAFB;
            margin-bottom: 5px;
        }

        .card-text {
            font-size: 10px;
            color: #9CA3AF;
            line-height: 1.5;
        }

        .date-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .date-item {
            display: table-cell;
            width: 50%;
        }

        .date-label {
            font-size: 8px;
            color: #6B7280;
            text-transform: uppercase;
        }

        .date-value {
            font-size: 12px;
            font-weight: 700;
            color: #D97706;
            margin-top: 2px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #D97706;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border-radius: 16px;
            overflow: hidden;
        }

        .items-table thead {
            background: #111827;
        }

        .items-table th {
            padding: 16px 14px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #D97706;
            border-bottom: 2px solid #D97706;
        }

        .items-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #374151;
            font-size: 11px;
            color: #D1D5DB;
            background: #111827;
        }

        .items-table th.text-right,
        .items-table td.text-right {
            text-align: right;
        }

        .items-table th.text-center,
        .items-table td.text-center {
            text-align: center;
        }

        .item-main {
            font-weight: 700;
            color: #F9FAFB;
            font-size: 12px;
        }

        .item-sub {
            font-size: 9px;
            color: #6B7280;
            margin-top: 3px;
        }

        .item-total {
            font-weight: 800;
            color: #D97706;
            font-size: 12px;
        }

        .bottom-section {
            display: table;
            width: 100%;
        }

        .left-section {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 25px;
        }

        .right-section {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }

        .payment-box {
            background: #111827;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid #374151;
        }

        .payment-box h4 {
            font-size: 14px;
            font-weight: 700;
            color: #D97706;
            margin-bottom: 15px;
        }

        .payment-method {
            background: #1F2937;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 10px;
            display: table;
            width: 100%;
            border: 1px solid #374151;
        }

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

        .pm-details {
            display: table-cell;
            vertical-align: middle;
        }

        .pm-name {
            font-size: 10px;
            color: #6B7280;
            margin-bottom: 2px;
        }

        .pm-number {
            font-size: 14px;
            font-weight: 700;
            color: #D97706;
            font-family: monospace;
            letter-spacing: 1px;
        }

        .totals-box {
            background: #111827;
            border-radius: 20px;
            padding: 25px;
            border: 1px solid #374151;
        }

        .totals-row {
            display: table;
            width: 100%;
            padding: 10px 0;
        }

        .totals-row .label {
            display: table-cell;
            font-size: 12px;
            color: #9CA3AF;
        }

        .totals-row .value {
            display: table-cell;
            text-align: right;
            font-size: 12px;
            color: #F9FAFB;
            font-weight: 600;
        }

        .totals-row.discount .value {
            color: #EF4444;
        }

        .totals-divider {
            height: 2px;
            background: linear-gradient(90deg, #D97706, #F59E0B, #D97706);
            border-radius: 1px;
            margin: 15px 0;
        }

        .totals-row.grand-total {
            padding: 15px 0 0 0;
        }

        .totals-row.grand-total .label {
            font-size: 14px;
            font-weight: 700;
            color: #F9FAFB;
        }

        .totals-row.grand-total .value {
            font-size: 26px;
            font-weight: 800;
            color: #D97706;
        }

        .amount-words {
            margin-top: 15px;
            padding: 12px;
            background: #1F2937;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #374151;
        }

        .amount-words p {
            font-size: 10px;
            color: #9CA3AF;
            font-style: italic;
        }

        .notes-box {
            margin-top: 25px;
            background: #111827;
            border-radius: 16px;
            padding: 20px;
            border-left: 4px solid #D97706;
        }

        .notes-box h4 {
            font-size: 11px;
            font-weight: 700;
            color: #D97706;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notes-box p {
            font-size: 11px;
            color: #9CA3AF;
            line-height: 1.6;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
        }

        .footer-content {
            background: #111827;
            border: 2px solid #D97706;
            border-radius: 20px;
            padding: 25px;
        }

        .footer-thanks {
            font-size: 20px;
            font-weight: 800;
            color: #D97706;
            margin-bottom: 10px;
        }

        .footer-company {
            font-size: 12px;
            color: #F9FAFB;
        }

        .footer-contact {
            font-size: 11px;
            color: #9CA3AF;
            margin-top: 8px;
        }

        .footer-badge {
            display: inline-block;
            margin-top: 15px;
            padding: 6px 16px;
            background: #D97706;
            color: #1F2937;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="gold-accent"></div>

        <div class="container">
            <div class="header">
                <div class="header-left">
                    <div class="brand">
                        <div class="brand-logo">
                            <div class="brand-logo-box">
                                {{ strtoupper(substr($invoice->user->company_name ?? ($invoice->user->name ?? 'P'), 0, 2)) }}
                            </div>
                        </div>
                        <div class="brand-info">
                            <div class="brand-name">
                                {{ $invoice->user->company_name ?? ($invoice->user->name ?? config('app.name')) }}</div>
                            <div class="brand-tagline">Premium Solution</div>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="invoice-meta">
                        <div class="invoice-type">Facture</div>
                        <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                        <span class="status-pill status-{{ $invoice->status }}">
                            @switch($invoice->status)
                                @case('paid')
                                    ✓ Payée
                                @break

                                @case('pending')
                                    ⏳ En attente
                                @break

                                @case('overdue')
                                    ⚠ En retard
                                @break

                                @case('sent')
                                    ✉ Envoyée
                                @break

                                @default
                                    📝 Brouillon
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>

            <div class="cards-section">
                <div class="card">
                    <div class="card-icon from">📤</div>
                    <div class="card-label">Émetteur</div>
                    <div class="card-title">
                        {{ $invoice->user->company_name ?? ($invoice->user->name ?? config('app.name')) }}</div>
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
                        @if ($invoice->client->phone)
                            {{ $invoice->client->phone }}<br>
                        @endif
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
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>
                                <div class="item-main">{{ $item->description }}</div>
                                @if ($item->product && $item->product->sku)
                                    <div class="item-sub">Référence: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} XOF</td>
                            <td class="text-right">{{ number_format($item->tax_rate, 0) }}%</td>
                            <td class="text-right"><span
                                    class="item-total">{{ number_format($item->total, 0, ',', ' ') }} XOF</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="bottom-section">
                <div class="left-section">
                    @if (
                        ($invoice->user->wave_number ?? false) ||
                            ($invoice->user->orange_money_number ?? false) ||
                            ($invoice->user->momo_number ?? false) ||
                            ($invoice->user->moov_money_number ?? false))
                        <div class="payment-box">
                            <h4>💳 Paiement Mobile</h4>
                            @if ($invoice->user->wave_number ?? false)
                                <div class="payment-method">
                                    <div class="pm-icon">
                                        <div class="pm-icon-box" style="background:#1dc0f2;">🌊</div>
                                    </div>
                                    <div class="pm-details">
                                        <div class="pm-name">Wave</div>
                                        <div class="pm-number">{{ $invoice->user->wave_number }}</div>
                                    </div>
                                </div>
                            @endif
                            @if ($invoice->user->orange_money_number ?? false)
                                <div class="payment-method">
                                    <div class="pm-icon">
                                        <div class="pm-icon-box" style="background:#ff7900;">🍊</div>
                                    </div>
                                    <div class="pm-details">
                                        <div class="pm-name">Orange Money</div>
                                        <div class="pm-number">{{ $invoice->user->orange_money_number }}</div>
                                    </div>
                                </div>
                            @endif
                            @if ($invoice->user->momo_number ?? false)
                                <div class="payment-method">
                                    <div class="pm-icon">
                                        <div class="pm-icon-box" style="background:#ffcc00;">📱</div>
                                    </div>
                                    <div class="pm-details">
                                        <div class="pm-name">MTN Mobile Money</div>
                                        <div class="pm-number">{{ $invoice->user->momo_number }}</div>
                                    </div>
                                </div>
                            @endif
                            @if ($invoice->user->moov_money_number ?? false)
                                <div class="payment-method">
                                    <div class="pm-icon">
                                        <div class="pm-icon-box" style="background:#0066b3;">📲</div>
                                    </div>
                                    <div class="pm-details">
                                        <div class="pm-name">Moov Money</div>
                                        <div class="pm-number">{{ $invoice->user->moov_money_number }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="right-section">
                    <div class="totals-box">
                        <div class="totals-row">
                            <div class="label">Sous-total HT</div>
                            <div class="value">{{ number_format($invoice->subtotal, 0, ',', ' ') }} XOF</div>
                        </div>
                        @if ($invoice->tax_amount > 0)
                            <div class="totals-row">
                                <div class="label">TVA (18%)</div>
                                <div class="value">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} XOF</div>
                            </div>
                        @endif
                        @if ($invoice->discount_amount > 0)
                            <div class="totals-row discount">
                                <div class="label">Remise accordée</div>
                                <div class="value">- {{ number_format($invoice->discount_amount, 0, ',', ' ') }} XOF
                                </div>
                            </div>
                        @endif
                        <div class="totals-divider"></div>
                        <div class="totals-row grand-total">
                            <div class="label">Total TTC</div>
                            <div class="value">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</div>
                        </div>
                        <div class="amount-words">
                            <p>Arrêté la présente facture à la somme de
                                :<br><strong>{{ \App\Helpers\NumberToWords::convert($invoice->total) }} Francs
                                    CFA</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($invoice->notes)
                <div class="notes-box">
                    <h4>📝 Notes & Conditions</h4>
                    <p>{{ $invoice->notes }}</p>
                </div>
            @endif

            <div class="footer">
                <div class="footer-content">
                    <div class="footer-thanks">Merci pour votre confiance ! 🏆</div>
                    <div class="footer-company">{{ $invoice->user->company_name ?? config('app.name') }}</div>
                    <div class="footer-contact">
                        {{ $invoice->user->email ?? '' }}
                        @if ($invoice->user->phone ?? false)
                            • {{ $invoice->user->phone }}
                        @endif
                    </div>
                    <div class="footer-badge">★ Premium</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
