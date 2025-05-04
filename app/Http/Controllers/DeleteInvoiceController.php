<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DeleteInvoiceController extends Controller
{
    public function __invoke(int $id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::with('items')->find($id);

            if (!$invoice) {
                DB::rollBack();
                return response()->json(['message' => 'Invoice not found.'], 404);
            }

            if ((int) $invoice->status === InvoiceStatus::PAID->value
                || (int) $invoice->status === InvoiceStatus::PARTIALLY_PAID->value) {
                DB::rollBack();
                return response()->json(['message' => 'Partially paid and paid invoices cannot be deleted.'], 403);
            }

            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to delete invoice. Call the support.'], 500);
        }
    }
}
