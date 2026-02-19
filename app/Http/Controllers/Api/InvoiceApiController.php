<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\InvoiceItem;
use App\Services\InvoiceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvoiceApiController extends Controller
{
    protected InvoiceCalculatorService $calculator;

    public function __construct(InvoiceCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $invoices = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->with(['client', 'items'])
            ->latest()
            ->paginate(15);

        return response()->json($invoices);
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'due_date' => ['required', 'date', 'after:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify client belongs to user's tenant
        $client = Client::where('id', $request->client_id)
            ->where('tenant_id', $request->user()->tenant_id)
            ->firstOrFail();

        // Calculate totals using items with 'quantity' field
        $items = collect($request->items)->map(function ($item) {
            return [
                'qty' => $item['quantity'], // Map quantity to qty for calculator
                'unit_price' => $item['unit_price']
            ];
        })->toArray();
        
        $subtotal = $this->calculator->calculateSubtotal($items);
        
        $taxRate = $request->input('tax_rate', 0);
        $tax = $this->calculator->calculateTax($subtotal, $taxRate);
        
        $discount = $request->input('discount', 0);
        $total = $this->calculator->calculateTotal($subtotal, $tax, $discount);

        // Create invoice
        $invoice = Invoice::create([
            'tenant_id' => $request->user()->tenant_id,
            'user_id' => $request->user()->id,
            'client_id' => $client->id,
            'number' => $this->generateInvoiceNumber(),
            'due_date' => $request->due_date,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'status' => 'draft',
            'notes' => $request->input('notes'),
        ]);

        // Create invoice items
        foreach ($request->items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $itemTotal,
            ]);
        }

        // Reload with relationships
        $invoice->load(['client', 'items']);

        return response()->json([
            'success' => true,
            'data' => [
                'invoice' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->number,
                    'client' => $invoice->client,
                    'subtotal' => $invoice->subtotal,
                    'tax_amount' => $invoice->tax,
                    'total_amount' => $invoice->total,
                    'status' => $invoice->status,
                    'items' => $invoice->items,
                ]
            ],
            'message' => 'Invoice created successfully'
        ], 201);
    }

    /**
     * Display the specified invoice.
     */
    public function show(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->with(['client', 'items'])
            ->findOrFail($id);

        return response()->json($invoice);
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'due_date' => ['sometimes', 'date'],
            'status' => ['sometimes', Rule::in(['draft', 'sent', 'viewed', 'partially_paid', 'paid', 'overdue'])],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $invoice->update($request->only(['client_id', 'due_date', 'status', 'notes']));

        $invoice->load(['client', 'items']);

        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice
        ]);
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }

    /**
     * Generate PDF for the invoice.
     */
    public function generatePdf(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->with(['client', 'items'])
            ->findOrFail($id);

        // Mock PDF generation (implement real PDF service later)
        $pdfPath = 'invoices/invoice-' . $invoice->number . '.pdf';
        
        $invoice->update(['pdf_path' => $pdfPath]);

        return response()->json([
            'success' => true,
            'data' => [
                'pdf_path' => $pdfPath
            ],
            'message' => 'PDF generated successfully'
        ]);
    }

    /**
     * Download PDF for the invoice.
     */
    public function downloadPdf(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->findOrFail($id);

        if (!$invoice->pdf_path) {
            return response()->json([
                'success' => false,
                'message' => 'PDF not generated yet'
            ], 404);
        }

        // Mock PDF download (implement real file download later)
        return response()->json([
            'success' => true,
            'data' => [
                'pdf_path' => $invoice->pdf_path,
                'download_url' => url('storage/' . $invoice->pdf_path)
            ],
            'message' => 'PDF ready for download'
        ]);
    }

    /**
     * Generate unique invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->number, -4)) + 1 : 1;

        return 'INV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
