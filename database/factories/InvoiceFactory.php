<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => 'FAT-' . str_pad($this->faker->unique()->numberBetween(1, 99999999), 8, '0', STR_PAD_LEFT),
            'issue_date' => $this->faker->dateTimeBetween('-1 months'),
            'amount_paid' => $this->faker->randomFloat(2, 0.01, 100.00),
            'status' => $this->faker->randomElement(array_column(InvoiceStatus::cases(), 'value')),
        ];
    }
}
