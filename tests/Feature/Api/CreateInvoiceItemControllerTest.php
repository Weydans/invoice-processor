<?php

namespace Tests\Feature\Api;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateInvoiceItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_invoice_item_and_returns_expected_structure()
    {
        $invoice = Invoice::factory()->create(['status' => InvoiceStatus::PENDING->value]);

        $payload = [
            'invoice_id' => $invoice->id,
            'description' => fake()->sentence(),
            'value' => fake()->randomFloat(2, 0.01, 999999),
            'percentage_paid' => fake()->randomFloat(2, 0.00, 100.00),
        ];

        $response = $this->postJson('/api/invoice-item', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'invoice_id',
                    'description',
                    'value',
                    'percentage_paid',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'invoice_id' => $payload['invoice_id'],
                    'description' => $payload['description'],
                    'value' => $payload['value'],
                    'percentage_paid' => $payload['percentage_paid'],
                ]
            ]);

        $this->assertDatabaseHas('invoice_items', $payload);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('restrictedInvoiceStatuses')]
    public function test_it_fails_to_create_if_invoice_status_is_restricted($status)
    {
        $invoice = Invoice::factory()->create(['status' => $status->value]);

        $payload = [
            'invoice_id' => $invoice->id,
            'description' => fake()->sentence,
            'value' => fake()->randomFloat(2, 0.01, 999999),
            'percentage_paid' => fake()->randomFloat(2, 0, 100),
        ];

        $response = $this->postJson('/api/invoice-item', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('invoice_item');

        $this->assertDatabaseMissing('invoice_items', ['invoice_id' => $invoice->id]);
    }

    public static function restrictedInvoiceStatuses(): array
    {
        return [
            'partially_paid' => [InvoiceStatus::PARTIALLY_PAID],
            'paid' => [InvoiceStatus::PAID],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidInvoiceItemDataProvider')]
    public function test_it_fails_validation_with_invalid_data(array $invalidData, string $expectedField)
    {
        $invoice = Invoice::factory()->create();

        // Merge valid data with the invalid fields
        $validData = [
            'invoice_id' => $invoice->id,
            'description' => fake()->sentence(),
            'value' => 1000.00,
            'percentage_paid' => 50.00,
        ];

        $payload = array_merge($validData, $invalidData);

        $response = $this->postJson('/api/invoice-item', $payload);

        $response->assertStatus(422)->assertJsonValidationErrors($expectedField);
    }

    public static function invalidInvoiceItemDataProvider(): array
    {
        return [
            'missing invoice_id' => [['invoice_id' => null], 'invoice_id'],
            'invoice_id is non-existent' => [['invoice_id' => 999999], 'invoice_id'],
            'missing description' => [['description' => null], 'description'],
            'description is not a string' => [['description' => 12345], 'description'],
            'missing value' => [['value' => null], 'value'],
            'value is not numeric' => [['value' => 'abc'], 'value'],
            'value is below minimum' => [['value' => -0.01], 'value'],
            'missing percentage_paid' => [['percentage_paid' => null], 'percentage_paid'],
            'percentage_paid is not numeric' => [['percentage_paid' => 'abc'], 'percentage_paid'],
            'percentage_paid exceeds 100' => [['percentage_paid' => 100.01], 'percentage_paid'],
            'percentage_paid is below 0' => [['percentage_paid' => -0.01], 'percentage_paid'],
        ];
    }
}
