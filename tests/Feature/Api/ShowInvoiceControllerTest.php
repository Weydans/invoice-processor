<?php

namespace Tests\Feature\Api;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_an_invoice_with_its_items()
    {
        /** @var Invoice $item */
        $item = Invoice::factory()->create();

        $this->withoutExceptionHandling();

        $response = $this->getJson("/api/invoice/{$item->id}");

        $response->assertStatus(200)
             ->assertJsonStructure([
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
                     ]
                 ]
             ]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidIdProvider')]
    public function test_it_fails_validation_for_invalid_id($id)
    {
        $response = $this->getJson("/api/invoice-item/{$id}");

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
