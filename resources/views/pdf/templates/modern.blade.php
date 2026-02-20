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
            color: #333;
            line-height: 1.5;
        }

        .container {
            padding: 30px;
        }

        .accent-bar {
            height: 6px;
            background: linear-gradient(90deg, #8B5CF6, #6D28D9, #A78BFA);
            margin-bottom: 0;
        }

        .header {
            margin-bottom: 30px;
            padding: 20px 0;
        }

        .header-table {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: right;
        }

        .header h1 {
            color: #6D28D9;
            font-size: 28px;
            letter-spacing: -0.5px;
        }

        .invoice-number {
            font-size: 13px;
            color: #8B5CF6;
            margin-top: 4px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: #DCFCE7;
            color: #166534;
        }

        .status-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-overdue {
            background: #FEE2E2;
            color: #991B1B;
        }

        .status-draft {
            background: #F3F4F6;
            color: #6B7280;
        }

        .status-sent {
            background: #EDE9FE;
            color: #6D28D9;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-block {
            margin-bottom: 15px;
            padding: 15px;
            background: #F5F3FF;
            border-radius: 8px;
        }

        .info-block h3 {
            font-size: 10px;
            color: #8B5CF6;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .info-block p {
            margin: 2px 0;
            font-size: 11px;
            color: #4B5563;
        }

        .info-block .name {
            font-size: 13px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 4px;
        }

        .dates-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .date-item {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 12px;
            background: #F5F3FF;
            border-radius: 8px;
        }

        .date-item .label {
            font-size: 9px;
            color: #8B5CF6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .date-item .value {
            font-size: 13px;
            font-weight: 700;
            color: #1F2937;
            margin-top: 3px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table thead {
            background: linear-gradient(135deg, #8B5CF6, #6D28D9);
        }

        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            color: #fff;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #F3F4F6;
            font-size: 11px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #FAFAFA;
        }

        .items-table th.text-right,
        .items-table td.text-right {
            text-align: right;
        }

        .items-table th.text-center,
        .items-table td.text-center {
            text-align: center;
        }

        .totals {
            width: 260px;
            margin-left: auto;
            margin-bottom: 25px;
            background: #F5F3FF;
            border-radius: 8px;
            padding: 15px;
        }

        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .totals-row .label {
            display: table-cell;
            text-align: left;
            font-size: 11px;
            color: #6B7280;
        }

        .totals-row .value {
            display: table-cell;
            text-align: right;
            font-size: 11px;
            color: #1F2937;
        }

        .totals-row.total {
            font-size: 16px;
            font-weight: 700;
            color: #6D28D9;
            padding-top: 10px;
            border-top: 2px solid #8B5CF6;
            margin-top: 8px;
        }

        .totals-row.total .label {
            color: #6D28D9;
            font-size: 13px;
        }

        .totals-row.total .value {
            color: #6D28D9;
            font-size: 16px;
        }

        .notes {
            margin-top: 20px;
            padding: 14px;
            background: #F5F3FF;
            border-left: 4px solid #8B5CF6;
            border-radius: 0 8px 8px 0;
            font-size: 10px;
        }

        .notes h4 {
            font-size: 10px;
            margin-bottom: 5px;
            color: #8B5CF6;
            font-weight: 700;
        }

        .footer {
            margin-top: 40px;
            padding: 15px;
            background: linear-gradient(135deg, #8B5CF6, #6D28D9);
            border-radius: 8px;
            text-align: center;
            font-size: 10px;
            color: #fff;
        }

        .footer .thanks {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="accent-bar"></div>
    <div class="container">
        <div class="header">
            <div class="header-table">
                <div class="header-left">
                    <h1>FACTURE</h1>
                    <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                </div>
                <div class="header-right">
                    <span class="status-badge status-{{ $invoice->status }}">
                        @switch($invoice->status)
                            @case('paid')
                                Payée
                            @break

                            @case('pending')
                                En attente
                            @break

                            @case('overdue')
                                En retard
                            @break

                            @case('sent')
                                Envoyée
                            @break

                            @default
                                Brouillon
                        @endswitch
                    </span>
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-column" style="padding-right: 10px;">
                <div class="info-block">
                    <h3>Émetteur</h3>
                    <p class="name">{{ $invoice->user->company_name ?? ($invoice->user->name ?? config('app.name')) }}
                    </p>
                    <p>{{ $invoice->user->email ?? '' }}</p>
                    <p>{{ $invoice->user->phone ?? '' }}</p>
                    @if ($invoice->user->address ?? false)
                        <p>{{ $invoice->user->address }}</p>
                    @endif
                </div>
            </div>
            <div class="info-column" style="padding-left: 10px;">
                <div class="info-block">
                    <h3>Client</h3>
                    <p class="name">{{ $invoice->client->name }}</p>
                    <p>{{ $invoice->client->email }}</p>
                    @if ($invoice->client->phone)
                        <p>{{ $invoice->client->phone }}</p>
                    @endif
                    @if ($invoice->client->address)
                        <p>{{ $invoice->client->address }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="dates-row">
            <div class="date-item" style="margin-right: 8px;">
                <div class="label">Date d'émission</div>
                <div class="value">{{ $invoice->issue_date->format('d/m/Y') }}</div>
            </div>
            <div class="date-item" style="margin-left: 4px; margin-right: 4px;">
                <div class="label">Date d'échéance</div>
                <div class="value">{{ $invoice->due_date->format('d/m/Y') }}</div>
            </div>
            <div class="date-item" style="margin-left: 8px;">
                <div class="label">Montant total</div>
                <div class="value">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</div>
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
                @foreach ($invoice->items as $item)
                    <tr>
                        <td><strong>{{ $item->description }}</strong></td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} XOF</td>
                        <td class="text-right">{{ number_format($item->tax_rate, 0) }}%</td>
                        <td class="text-right"><strong>{{ number_format($item->total, 0, ',', ' ') }} XOF</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <div class="label">Sous-total:</div>
                <div class="value">{{ number_format($invoice->subtotal, 0, ',', ' ') }} XOF</div>
            </div>
            @if ($invoice->tax_amount > 0)
                <div class="totals-row">
                    <div class="label">TVA:</div>
                    <div class="value">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} XOF</div>
                </div>
            @endif
            @if ($invoice->discount_amount > 0)
                <div class="totals-row">
                    <div class="label">Remise:</div>
                    <div class="value">-{{ number_format($invoice->discount_amount, 0, ',', ' ') }} XOF</div>
                </div>
            @endif
            <div class="totals-row total">
                <div class="label">TOTAL:</div>
                <div class="value">{{ number_format($invoice->total, 0, ',', ' ') }} XOF</div>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="notes">
                <h4>Notes</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <div class="footer">
            <p class="thanks">Merci pour votre confiance ! ✨</p>
            <p>{{ $invoice->user->company_name ?? config('app.name') }}</p>
            <p>{{ $invoice->user->email ?? '' }} {{ $invoice->user->phone ? '• ' . $invoice->user->phone : '' }}</p>
        </div>
    </div>
</body>

</html>
