<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\ListInvoiceRequest;

class ListInvoiceController extends Controller
{
    public function __invoke(ListInvoiceRequest $request)
    {
        $perPage = $request->input('per_page', 100);

        $items = Invoice::paginate($perPage);

        return response()->json($items);
    }
}
