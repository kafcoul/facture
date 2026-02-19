<?php

namespace App\Console\Commands;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Models\InvoiceItem;
use App\Domain\Invoice\Models\RecurringInvoice;
use App\Mail\InvoiceSentMail;
use App\Services\InvoiceNumberService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GenerateRecurringInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'invoices:generate-recurring {--dry-run : Ne pas créer les factures, juste afficher}';

    /**
     * The console command description.
     */
    protected $description = 'Générer automatiquement les factures récurrentes dues';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceNumberService $invoiceNumberService): int
    {
        $dryRun = $this->option('dry-run');
        $recurring = RecurringInvoice::due()->with(['client', 'user'])->get();

        if ($recurring->isEmpty()) {
            $this->info('Aucune facture récurrente à générer.');
            return Command::SUCCESS;
        }

        $this->info("Trouvé {$recurring->count()} facture(s) récurrente(s) à générer.");

        $generated = 0;
        $errors = 0;

        foreach ($recurring as $recurrence) {
            if ($dryRun) {
                $this->line("  [DRY-RUN] Facture pour {$recurrence->client->name} — {$recurrence->total} {$recurrence->currency}");
                continue;
            }

            try {
                DB::transaction(function () use ($recurrence, $invoiceNumberService, &$generated) {
                    // Créer la facture
                    $invoice = Invoice::create([
                        'tenant_id' => $recurrence->tenant_id,
                        'user_id' => $recurrence->user_id,
                        'client_id' => $recurrence->client_id,
                        'recurring_invoice_id' => $recurrence->id,
                        'number' => $invoiceNumberService->generate($recurrence->tenant_id),
                        'uuid' => (string) Str::uuid(),
                        'type' => 'invoice',
                        'status' => 'draft',
                        'subtotal' => $recurrence->subtotal,
                        'tax' => $recurrence->tax,
                        'total' => $recurrence->total,
                        'currency' => $recurrence->currency,
                        'issued_at' => now(),
                        'due_date' => $recurrence->next_due_date,
                        'notes' => $recurrence->notes,
                        'terms' => $recurrence->terms,
                        'public_hash' => Str::random(32),
                        'metadata' => [
                            'generated_from' => 'recurring',
                            'recurring_id' => $recurrence->id,
                            'occurrence' => $recurrence->occurrences_count + 1,
                        ],
                    ]);

                    // Créer les lignes de facture
                    if ($recurrence->items) {
                        foreach ($recurrence->items as $item) {
                            InvoiceItem::create([
                                'tenant_id' => $recurrence->tenant_id,
                                'invoice_id' => $invoice->id,
                                'product_id' => $item['product_id'] ?? null,
                                'description' => $item['description'] ?? '',
                                'quantity' => $item['quantity'] ?? 1,
                                'unit_price' => $item['unit_price'] ?? 0,
                                'tax_rate' => $item['tax_rate'] ?? 0,
                                'discount' => $item['discount'] ?? 0,
                                'total' => $item['total'] ?? 0,
                            ]);
                        }
                    }

                    // Envoyer automatiquement si configuré
                    if ($recurrence->auto_send && $recurrence->client->email) {
                        $invoice->update(['status' => 'sent']);
                        Mail::to($recurrence->client->email)
                            ->send(new InvoiceSentMail($invoice));
                    }

                    // Mettre à jour la récurrence
                    $recurrence->update([
                        'occurrences_count' => $recurrence->occurrences_count + 1,
                        'next_due_date' => $recurrence->calculateNextDueDate(),
                        'last_generated_at' => now(),
                    ]);

                    // Désactiver si limite atteinte
                    if ($recurrence->occurrences_limit &&
                        $recurrence->occurrences_count >= $recurrence->occurrences_limit) {
                        $recurrence->update(['is_active' => false]);
                    }

                    $generated++;
                });

                $this->info("  ✅ Facture générée pour {$recurrence->client->name}");
            } catch (\Exception $e) {
                $errors++;
                Log::error('Erreur génération facture récurrente', [
                    'recurring_id' => $recurrence->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ❌ Erreur pour {$recurrence->client->name}: {$e->getMessage()}");
            }
        }

        if (!$dryRun) {
            $this->newLine();
            $this->info("Résumé: {$generated} facture(s) générée(s), {$errors} erreur(s).");
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
