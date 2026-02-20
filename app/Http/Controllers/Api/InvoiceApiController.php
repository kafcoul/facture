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
     * @OA\Get(
     *     path="/v1/invoices",
     *     summary="Lister les factures du tenant",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", description="Numéro de page", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des factures",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="number", type="string", example="INV-2025-0001"),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="total", type="number", format="float", example=150000),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2025-08-01"),
     *                 @OA\Property(property="client", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Entreprise ABC")
     *                 )
     *             )),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=3),
     *             @OA\Property(property="total", type="integer", example=42)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
     * @OA\Post(
     *     path="/v1/invoices",
     *     summary="Créer une nouvelle facture",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id","due_date","items"},
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-08-01"),
     *             @OA\Property(property="tax_rate", type="number", example=18),
     *             @OA\Property(property="discount", type="number", example=5000),
     *             @OA\Property(property="notes", type="string", example="Merci pour votre confiance"),
     *             @OA\Property(property="items", type="array", @OA\Items(
     *                 required={"description","quantity","unit_price"},
     *                 @OA\Property(property="description", type="string", example="Développement site web"),
     *                 @OA\Property(property="quantity", type="number", example=1),
     *                 @OA\Property(property="unit_price", type="number", example=500000),
     *                 @OA\Property(property="product_id", type="integer", example=null, nullable=true)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Facture créée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="invoice", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="invoice_number", type="string", example="INV-2025-0001"),
     *                     @OA\Property(property="subtotal", type="number", example=500000),
     *                     @OA\Property(property="tax_amount", type="number", example=90000),
     *                     @OA\Property(property="total_amount", type="number", example=585000),
     *                     @OA\Property(property="status", type="string", example="draft")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Invoice created successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
     * @OA\Get(
     *     path="/v1/invoices/{id}",
     *     summary="Afficher une facture",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la facture",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la facture avec client et items",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="number", type="string", example="INV-2025-0001"),
     *             @OA\Property(property="status", type="string", example="draft"),
     *             @OA\Property(property="subtotal", type="number", example=500000),
     *             @OA\Property(property="tax", type="number", example=90000),
     *             @OA\Property(property="total", type="number", example=585000),
     *             @OA\Property(property="due_date", type="string", format="date"),
     *             @OA\Property(property="client", type="object"),
     *             @OA\Property(property="items", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Facture non trouvée"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function show(Request $request, $id)
    {
        $invoice = Invoice::where('tenant_id', $request->user()->tenant_id)
            ->with(['client', 'items'])
            ->findOrFail($id);

        return response()->json($invoice);
    }

    /**
     * @OA\Put(
     *     path="/v1/invoices/{id}",
     *     summary="Mettre à jour une facture",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la facture",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="client_id", type="integer", example=2),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-09-01"),
     *             @OA\Property(property="status", type="string", enum={"draft","sent","viewed","partially_paid","paid","overdue"}, example="sent"),
     *             @OA\Property(property="notes", type="string", example="Mise à jour des conditions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Facture mise à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invoice updated successfully"),
     *             @OA\Property(property="invoice", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Facture non trouvée"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
     * @OA\Delete(
     *     path="/v1/invoices/{id}",
     *     summary="Supprimer une facture",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la facture",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Facture supprimée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invoice deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Facture non trouvée"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
     * @OA\Post(
     *     path="/v1/invoices/{id}/pdf",
     *     summary="Générer le PDF d'une facture",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la facture",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF généré",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="pdf_path", type="string", example="invoices/invoice-INV-2025-0001.pdf")
     *             ),
     *             @OA\Property(property="message", type="string", example="PDF generated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Facture non trouvée"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
     * @OA\Get(
     *     path="/v1/invoices/{id}/download",
     *     summary="Télécharger le PDF d'une facture",
     *     tags={"Invoices"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la facture",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL de téléchargement du PDF",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="pdf_path", type="string", example="invoices/invoice-INV-2025-0001.pdf"),
     *                 @OA\Property(property="download_url", type="string", example="http://localhost:8000/storage/invoices/invoice-INV-2025-0001.pdf")
     *             ),
     *             @OA\Property(property="message", type="string", example="PDF ready for download")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="PDF non encore généré",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="PDF not generated yet")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
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
