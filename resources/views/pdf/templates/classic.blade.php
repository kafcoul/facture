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

        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #1E40AF;
            font-size: 24px;
        }

        .invoice-number {
            font-size: 14px;
            color: #3B82F6;
            margin-top: 5px;
            font-weight: 600;
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
        }

        .info-block h3 {
            font-size: 11px;
            color: #3B82F6;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .info-block p {
            margin: 2px 0;
            font-size: 11px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table thead {
            background-color: #EFF6FF;
        }

        .items-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            color: #1E40AF;
            border-bottom: 2px solid #3B82F6;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 11px;
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
            width: 250px;
            margin-left: auto;
            margin-bottom: 25px;
        }

        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .totals-row .label {
            display: table-cell;
            text-align: right;
            padding-right: 15px;
            font-size: 11px;
        }

        .totals-row .value {
            display: table-cell;
            text-align: right;
            width: 100px;
            font-size: 11px;
        }

        .totals-row.total {
            font-size: 14px;
            font-weight: 700;
            color: #1E40AF;
            padding-top: 8px;
            border-top: 2px solid #3B82F6;
            margin-top: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #DCFCE7;
            color: #166534;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-overdue {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .status-draft {
            background-color: #F3F4F6;
            color: #6B7280;
        }

        .status-sent {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .notes {
            margin-top: 20px;
            padding: 12px;
            background-color: #EFF6FF;
            border-left: 3px solid #3B82F6;
            font-size: 10px;
        }

        .notes h4 {
            font-size: 10px;
            margin-bottom: 5px;
            color: #3B82F6;
            font-weight: 600;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #3B82F6;
            text-align: center;
            font-size: 9px;
            color: #9CA3AF;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>FACTURE</h1>
            <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
        </div>

        <div class="info-section">
            <div class="info-column">
                <div class="info-block">
                    <h3>De</h3>
                    <p><strong>{{ $invoice->user->company_name ?? ($invoice->user->name ?? config('app.name')) }}</strong>
                    </p>
                    <p>{{ $invoice->user->email ?? '' }}</p>
                    <p>{{ $invoice->user->phone ?? '' }}</p>
                </div>
            </div>
            <div class="info-column">
                <div class="info-block">
                    <h3>Facturé à</h3>
                    <p><strong>{{ $invoice->client->name }}</strong></p>
                    <p>{{ $invoice->client->email }}</p>
                    @if ($invoice->client->phone)
                        <p>{{ $invoice->client->phone }}</p>
                    @endif
                    @if ($invoice->client->address)
                        <p>{{ $invoice->client->address }}</p>
                    @endif
                </div>
                <div class="info-block">
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 2px 0; font-size: 10px;"><strong>Date:</strong></td>
                            <td style="text-align: right; font-size: 10px;">{{ $invoice->issue_date->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0; font-size: 10px;"><strong>Échéance:</strong></td>
                            <td style="text-align: right; font-size: 10px;">{{ $invoice->due_date->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0; font-size: 10px;"><strong>Statut:</strong></td>
                            <td style="text-align: right;"><span
                                    class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

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
            <p>Merci pour votre confiance !</p>
            <p>{{ $invoice->user->company_name ?? config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
