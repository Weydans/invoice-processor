<?php

namespace Tests\Feature\Api;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessInvoicePaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_processes_a_valid_payment()
    {
        // Create an invoice with items
        $invoice = Invoice::create([
            'number' => 1,
            'issue_date' => now(),
            'amount_paid' => 0,
            'status' => InvoiceStatus::PENDING->value,
        ]);

        $item1 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => fake()->unique()->sentence(),
            'value' => 40,
            'percentage_paid' => 0,
        ]);

        $item2 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => fake()->unique()->sentence(),
            'value' => 60,
            'percentage_paid' => 0,
        ]);

        // Send a valid payment of 50
        $response = $this->postJson("/api/invoice/{$invoice->id}/pay", ['amount' => 50]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Payment processed with success!']);

        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::PARTIALLY_PAID->value, $invoice->status);

        $item1->refresh();
        $item2->refresh();

        $percentage = $invoice->amount_paid / ($item1->value + $item2->value) * 100;

        $this->assertEquals($percentage, $item1->percentage_paid); // 40% of 50 = 20
        $this->assertEquals($percentage, $item2->percentage_paid); // 60% of 50 = 30
        $this->assertEquals(50, $invoice->amount_paid); // Total paid is 50
    }

    public function test_it_updates_invoice_status_to_paid_when_full_payment_is_made()
    {
        // Create an invoice
        $invoice = Invoice::create([
            'number' => 1,
            'issue_date' => now(),
            'amount_paid' => 0,
            'status' => InvoiceStatus::PENDING->value,
        ]);

        $item1 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => fake()->unique()->sentence(),
            'value' => 40,
            'percentage_paid' => 0,
        ]);

        $item2 = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => fake()->unique()->sentence(),
            'value' => 60,
            'percentage_paid' => 0,
        ]);

        $response = $this->postJson("/api/invoice/{$invoice->id}/pay", ['amount' => 100]);

        // Assert successful response
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Payment processed with success!']);

        // Assert the invoice status is 'PAGO'
        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::PAID->value, $invoice->status);

        // Assert all items are fully paid
        $item1->refresh();
        $item2->refresh();

        $this->assertEquals(100, $item1->percentage_paid);
        $this->assertEquals(100, $item2->percentage_paid);
        $this->assertEquals(100, $invoice->amount_paid); // Total paid is 100
    }

    public function test_it_fails_to_update_when_invoice_is_partially_paid_or_paid()
    {
        foreach ([InvoiceStatus::PARTIALLY_PAID, InvoiceStatus::PAID] as $status) {
            $invoice = Invoice::create([
                'number' => 123,
                'issue_date' => now(),
                'amount_paid' => 50,
                'status' => $status->value,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => fake()->unique()->sentence(),
                'value' => 40,
                'percentage_paid' => 50,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => fake()->unique()->sentence(),
                'value' => 60,
                'percentage_paid' => 50,
            ]);

            $response = $this->postJson("/api/invoice/{$invoice->id}/pay", ['amount' => 50]);

            $response->assertStatus(422)
                     ->assertJson([
                         'message' => 'Invoices partially paid or paid cannot be updated!',
                         'errors' => [
                             'id' => ['Invoices partially paid or paid cannot be updated!']
                         ]
                     ]);
        }
    }

    public function test_it_validates_the_amount_is_positive()
    {
        // Create an invoice
        $invoice = Invoice::create([
            'number' => 1,
            'issue_date' => now(),
            'amount_paid' => 0,
            'status' => InvoiceStatus::PENDING->value,
        ]);

        $response = $this->postJson("/api/invoice/{$invoice->id}/pay", ['amount' => -10]);

        // Assert validation error for amount
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['amount']);
    }
}
