<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Enums\InvoiceStatus;
use App\Http\Requests\CreateInvoiceRequest;
use Illuminate\Support\Facades\DB;

class CreateInvoiceController extends Controller
{
    public function __invoke(CreateInvoiceRequest $request)
    {
        DB::beginTransaction();

        try {
            $maxId = Invoice::max('id');

            /** @var Invoice $invoice */
            $invoice = Invoice::create([
                'number' => 'FAT-' . mb_str_pad(($maxId + 1), 8, '0', STR_PAD_LEFT),
                'issue_date' => now(),
                'amount_paid' => 0.00,
                'status' => InvoiceStatus::PENDING->value,
            ]);

            $items = $request->input('items');
            $validatedItems = array_map(function ($item) use ($invoice) {
                return [
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'value' => $item['value'],
                    'percentage_paid' => 0.00,
                ];
            }, $items);

            InvoiceItem::insert($validatedItems);

            DB::commit();

            return response()->json([
                'data' => $invoice->load('items'),
            ], 201);
        } catch (\PDOException $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while creating the invoice.',
                'error' => 'Ooops! Contact the support.',
                // 'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while creating the invoice.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
