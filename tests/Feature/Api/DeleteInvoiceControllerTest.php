<?php

namespace Tests\Feature\Api;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_invoice_with_pending_status_and_its_items()
    {
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::PENDING]);
        $items = InvoiceItem::factory()->count(2)->create(['invoice_id' => $invoice->id]);

        $response = $this->deleteJson("/api/invoice/{$invoice->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);

        foreach ($items as $item) {
            $this->assertDatabaseMissing('invoice_items', ['id' => $item->id]);
        }
    }

    public function test_it_does_not_allow_deleteing_invoice_with_partial_paid_status()
    {
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::PARTIALLY_PAID->value]);
        InvoiceItem::factory(2)->create(['invoice_id' => $invoice->id]);

        $response = $this->deleteJson("/api/invoice/{$invoice->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseHas('invoice_items', ['invoice_id' => $invoice->id]);
    }

    public function test_it_does_not_allow_deleting_invoice_with_paid_status()
    {
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::PAID->value]);
        InvoiceItem::factory(2)->create(['invoice_id' => $invoice->id]);

        $response = $this->deleteJson("/api/invoice/{$invoice->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseHas('invoice_items', ['invoice_id' => $invoice->id]);
    }

    public function test_it_returns_404_if_invoice_not_found()
    {
        $response = $this->deleteJson("/api/invoice/999999");

        $response->assertStatus(404);
    }
}
