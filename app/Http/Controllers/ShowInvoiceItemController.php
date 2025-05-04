<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Http\Requests\ShowInvoiceItemRequest;

class ShowInvoiceItemController extends Controller
{
    public function __invoke(ShowInvoiceItemRequest $request, int $id)
    {
        $invoiceItem = InvoiceItem::find($id);

        if (!$invoiceItem) {
            return response()->json(['message' => 'Invoice item not found.'], 404);
        }

        return response()->json($invoiceItem);
    }
}
