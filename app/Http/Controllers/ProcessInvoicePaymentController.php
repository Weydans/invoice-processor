<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use App\Http\Requests\ProcessInvoicePaymentRequest;

class ProcessInvoicePaymentController extends Controller
{
    public function __invoke(ProcessInvoicePaymentRequest $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status == InvoiceStatus::PARTIALLY_PAID->value
            || $invoice->status == InvoiceStatus::PAID->value) {
            return response()->json([
                'message' => 'Invoices partially paid or paid cannot be updated!',
                'errors' => ['id' => ['Invoices partially paid or paid cannot be updated!']]
            ], 422);
        }

        $totalItemsValue = $invoice->items->sum('value');
        $paymentAmount = $request->input('amount');

        if ($paymentAmount <= 0) {
            return response()->json(['error' => 'Amount must be bigger than zero!'], 400);
        }

        if ($paymentAmount > $totalItemsValue) {
            return response()->json([
                'message' => 'Amount cannot be bigger than all items value.',
                'errors' => ['amount' => ['Amount bigger than all items value.']]
            ], 422);
        }

        if ($paymentAmount < $totalItemsValue) {
            $invoice->amount_paid += $paymentAmount;
            $percentagePaid = $invoice->amount_paid / $totalItemsValue * 100;
            $invoice->status = InvoiceStatus::PARTIALLY_PAID->value; // Fully paid
            $invoice->items()->update(['percentage_paid' => $percentagePaid]);
        } else {
            $invoice->amount_paid = $totalItemsValue;
            $invoice->status = InvoiceStatus::PAID->value; // Fully paid
            $invoice->items()->update(['percentage_paid' => 100.00]);
        }

        $invoice->save();

        return response()->json(['message' => 'Payment processed with success!']);
    }
}
