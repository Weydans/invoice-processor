<?php

namespace Database\Seeders;

use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceItemSeeder extends Seeder
{
    public function run(): void
    {
        InvoiceItem::factory(fake()->numberBetween(1, 5))->create();
    }
}
