<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Http\Requests\ListInvoiceItemRequest;

class ListInvoiceItemController extends Controller
{
    public function __invoke(ListInvoiceItemRequest $request)
    {
        $perPage = $request->input('per_page', 10);

        $items = InvoiceItem::paginate($perPage);

        return response()->json($items);
    }
}
