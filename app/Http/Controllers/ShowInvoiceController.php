<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\ShowInvoiceRequest;

class ShowInvoiceController extends Controller
{
    public function __invoke(ShowInvoiceRequest $request, int $id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        return response()->json($invoice);
    }
}
