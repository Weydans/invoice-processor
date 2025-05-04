<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Enums\InvoiceStatus;
use App\Http\Requests\CreateInvoiceItemRequest;

class CreateInvoiceItemController extends Controller
{
    public function __invoke(CreateInvoiceItemRequest $request)
    {
        $invoice = Invoice::find($request->invoice_id);

        $status = InvoiceStatus::tryFrom($invoice->status);

        if (in_array($status, [InvoiceStatus::PARTIALLY_PAID, InvoiceStatus::PAID])) {
            return response()->json([
                'message' => 'Invoice item cannot be inserted because the invoice is finalized.',
                'errors' => ['invoice_item' => ['Invoice is locked (status is not insertable).']]
            ], 422);
        }

        $item = InvoiceItem::create($request->validated());

        return response()->json(['data' => $item], 201);
    }
}
