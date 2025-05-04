<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        Invoice::factory(1)->create();

        return [
            'invoice_id' => Invoice::inRandomOrder()->first()->id,
            'description' => $this->faker->sentence(),
            'value' => $this->faker->randomFloat(2, 0.01, 999.99),
            'percentage_paid' => $this->faker->randomFloat(2, 0.00, 100.00),
        ];
    }
}
