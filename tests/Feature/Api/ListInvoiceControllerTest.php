<?php

namespace Tests\Feature\Api;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Invoice::factory()->count(25)->create();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('paginationProvider')]
    public function test_it_returns_expected_pagination_structure($page, $perPage, $expectedCount)
    {
        $this->withoutExceptionHandling();

        $response = $this->getJson("/api/invoices?page={$page}&per_page={$perPage}");

        $response->assertStatus(200)
                 ->assertJson([
                     'current_page' => $page,
                     'per_page' => $perPage,
                 ]);

        if ($expectedCount > 0) {
            $response->assertJsonCount($expectedCount, 'data');
            $response->assertJsonStructure([
                 'current_page',
                 'data' => [
                     ['id', 'number', 'issue_date', 'amount_paid', 'status', 'created_at', 'updated_at']
                 ],
                 'first_page_url',
                 'from',
                 'last_page',
                 'last_page_url',
                 'links',
                 'next_page_url',
                 'path',
                 'per_page',
                 'prev_page_url',
                 'to',
                 'total',
             ]);
        } else {
            $this->assertEquals([], $response->json('data'));
        }
    }

    public static function paginationProvider(): array
    {
        return [
            'Page 1, 10 per page' => [1, 10, 10],
            'Page 2, 10 per page' => [2, 10, 10],
            'Page 3, 10 per page (last page)' => [3, 10, 5],
            'Page 1, 25 per page (all)' => [1, 25, 25],
            'Page 2, 25 per page (empty)' => [2, 25, 0],
        ];
    }


    #[\PHPUnit\Framework\Attributes\DataProvider('invalidPaginationProvider')]
    public function test_it_fails_validation_for_invalid_params($page, $perPage, $expectedErrors)
    {
        $response = $this->getJson("/api/invoices?page={$page}&per_page={$perPage}");

        $response->assertStatus(422)->assertJsonValidationErrors($expectedErrors);
    }

    public static function invalidPaginationProvider(): array
    {
        return [
            'Page is not an integer' => ['abc', 10, ['page']],
            'Per page too high' => [1, 999, ['per_page']],
            'Negative page' => [-1, 10, ['page']],
            'Zero per page' => [1, 0, ['per_page']],
        ];
    }
}
