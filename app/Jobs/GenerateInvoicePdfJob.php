<?php
namespace App\Jobs;
use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public Invoice $invoice){}
    public function handle(){ $path = PdfService::make($this->invoice); $this->invoice->update(['pdf_path'=>$path]); }
}
