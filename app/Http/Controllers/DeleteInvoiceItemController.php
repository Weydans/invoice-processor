<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Enums\InvoiceStatus;
use App\Http\Requests\DeleteInvoiceItemRequest;

class DeleteInvoiceItemController extends Controller
{
    public function __invoke(DeleteInvoiceItemRequest $request, int $id)
    {
        $invoiceItem = InvoiceItem::find($id);

        if (!$invoiceItem) {
            return response()->json(['message' => 'Invoice item not found.'], 404);
        }

        $status = InvoiceStatus::tryFrom($invoiceItem->invoice->status);

        if (in_array($status, [InvoiceStatus::PARTIALLY_PAID, InvoiceStatus::PAID])) {
            return response()->json([
                'message' => 'Invoice item cannot be deleted because the invoice is finalized.',
                'errors' => ['invoice_item' => ['Invoice is locked (status is not deletable).']]
            ], 422);
        }

        $invoiceItem->delete();

        return response()->json(null, 204);
    }
}
