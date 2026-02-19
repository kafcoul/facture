<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
            line-height: 1.6;
        }
        
        .container {
            padding: 30px;
        }
        
        .header {
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header .invoice-number {
            font-size: 14px;
            color: #666;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .info-block {
            margin-bottom: 20px;
        }
        
        .info-block h3 {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .info-block p {
            margin: 2px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table thead {
            background-color: #f3f4f6;
        }
        
        .items-table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
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
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        
        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .totals-row .label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-weight: 500;
        }
        
        .totals-row .value {
            display: table-cell;
            text-align: right;
            width: 120px;
        }
        
        .totals-row.total {
            font-size: 16px;
            font-weight: 700;
            color: #2563eb;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            margin-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 3px solid #2563eb;
        }
        
        .notes h4 {
            font-size: 12px;
            margin-bottom: 8px;
            color: #666;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>FACTURE</h1>
            <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
        </div>
        
        <!-- Info Section -->
        <div class="info-section">
            <div class="info-column">
                <div class="info-block">
                    <h3>De</h3>
                    <p><strong>{{ config('app.name') }}</strong></p>
                    <p>{{ env('COMPANY_ADDRESS', 'Adresse de votre entreprise') }}</p>
                    <p>{{ env('COMPANY_CITY', 'Ville, Code Postal') }}</p>
                    <p>{{ env('COMPANY_COUNTRY', 'Pays') }}</p>
                </div>
            </div>
            
            <div class="info-column">
                <div class="info-block">
                    <h3>Facturé à</h3>
                    <p><strong>{{ $invoice->client->name }}</strong></p>
                    <p>{{ $invoice->client->email }}</p>
                    @if($invoice->client->phone)
                        <p>{{ $invoice->client->phone }}</p>
                    @endif
                    @if($invoice->client->address)
                        <p>{{ $invoice->client->address }}</p>
                    @endif
                </div>
                
                <div class="info-block">
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 3px 0;"><strong>Date de facture:</strong></td>
                            <td style="padding: 3px 0; text-align: right;">{{ $invoice->issue_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Date d'échéance:</strong></td>
                            <td style="padding: 3px 0; text-align: right;">{{ $invoice->due_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0;"><strong>Statut:</strong></td>
                            <td style="padding: 3px 0; text-align: right;">
                                <span class="status-badge status-{{ $invoice->status }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-center" style="width: 10%;">Qté</th>
                    <th class="text-right" style="width: 15%;">Prix unit.</th>
                    <th class="text-right" style="width: 10%;">Taxe</th>
                    <th class="text-right" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->description }}</strong>
                        @if($item->product)
                            <br><small style="color: #666;">SKU: {{ $item->product->sku }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }} €</td>
                    <td class="text-right">{{ number_format($item->tax_rate, 1) }}%</td>
                    <td class="text-right"><strong>{{ number_format($item->total, 2) }} €</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <div class="label">Sous-total:</div>
                <div class="value">{{ number_format($invoice->subtotal, 2) }} €</div>
            </div>
            
            @if($invoice->tax_amount > 0)
            <div class="totals-row">
                <div class="label">TVA:</div>
                <div class="value">{{ number_format($invoice->tax_amount, 2) }} €</div>
            </div>
            @endif
            
            @if($invoice->discount_amount > 0)
            <div class="totals-row">
                <div class="label">Remise:</div>
                <div class="value">-{{ number_format($invoice->discount_amount, 2) }} €</div>
            </div>
            @endif
            
            <div class="totals-row total">
                <div class="label">TOTAL:</div>
                <div class="value">{{ number_format($invoice->total, 2) }} €</div>
            </div>
        </div>
        
        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <h4>Notes</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Merci pour votre confiance !</p>
            <p>{{ config('app.name') }} - {{ env('COMPANY_EMAIL', 'contact@example.com') }}</p>
        </div>
    </div>
</body>
</html>
