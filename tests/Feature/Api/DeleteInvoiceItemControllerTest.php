<?php

namespace Tests\Feature\Api;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteInvoiceItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_an_invoice_item_when_invoice_status_allows_it()
    {
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::PENDING->value]);
        $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

        $response = $this->deleteJson("/api/invoice-item/{$item->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('invoice_items', ['id' => $item->id]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('restrictedInvoiceStatuses')]
    public function test_it_does_not_delete_if_invoice_status_is_restricted(InvoiceStatus $status)
    {
        $invoice = Invoice::factory()->create(['status' => $status->value]);
        $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id]);

        $response = $this->deleteJson("/api/invoice-item/{$item->id}");

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('invoice_item');

        $this->assertDatabaseHas('invoice_items', ['id' => $item->id]);
    }

    public static function restrictedInvoiceStatuses(): array
    {
        return [
            'partially_paid' => [InvoiceStatus::PARTIALLY_PAID],
            'paid' => [InvoiceStatus::PAID],
        ];
    }

    public function test_it_returns_404_if_invoice_item_not_found()
    {
        $response = $this->deleteJson("/api/invoice-item/999999");

        $response->assertStatus(404);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidIdProvider')]
    public function test_it_fails_validation_for_invalid_id($id)
    {
        $response = $this->deleteJson("/api/invoice-item/{$id}");

        $response->assertStatus(422);
    }

    public static function invalidIdProvider(): array
    {
        return [
            'Id is not an integer' => ['abc'],
            'Id is zero' => [0],
            'Negative id' => [-1],
        ];
    }
}
