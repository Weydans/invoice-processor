<?php

namespace Tests\Feature\Api;

use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowInvoiceItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_an_invoice_item()
    {
        /** @var InvoiceItem $item */
        $item = InvoiceItem::factory()->create();

        $this->withoutExceptionHandling();

        $response = $this->getJson("/api/invoice-item/{$item->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'invoice_id',
                'description',
                'value',
                'percentage_paid',
                'created_at',
                'updated_at'
            ]);
    }

    public function test_it_returns_404_if_invoice_item_not_found()
    {
        $response = $this->deleteJson("/api/invoice-item/999999");

        $response->assertStatus(404);
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
