<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_invoice_with_items()
    {
        $this->withoutExceptionHandling();

        $payload = $this->getValidInvoicePayload();

        $response = $this->postJson('/api/invoice', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'number',
                         'issue_date',
                         'amount_paid',
                         'status',
                         'created_at',
                         'updated_at',
                         'items' => [
                             '*' => [
                                'id',
                                'invoice_id',
                                'description',
                                'value',
                                'percentage_paid',
                                'created_at',
                                'updated_at',
                             ],
                         ],
                     ]
                 ]);

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseCount('invoice_items', count($payload['items']));
    }

    public function test_it_fails_if_items_are_missing()
    {
        $response = $this->postJson('/api/invoice', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('items');
    }

    public function test_it_fails_if_item_has_invalid_description_or_value()
    {
        $payload = [
            'items' => [
                ['value' => 500.00],
                ['description' => 'Incomplete'],
                ['value' => 500.00, 'description' => 'ab'],
            ],
        ];

        $response = $this->postJson('/api/invoice', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'items.0.description',
                     'items.1.value',
                     'items.2.description',
                 ]);
    }

    /**
     * Helper function to generate valid invoice payload with Faker
     */
    protected function getValidInvoicePayload()
    {
        return [
            'items' => [
                [
                    'description' => fake()->sentence,
                    'value' => fake()->randomFloat(2, 10, 1000),
                ],
                [
                    'description' => fake()->sentence,
                    'value' => fake()->randomFloat(2, 10, 1000),
                ]
            ]
        ];
    }
}
