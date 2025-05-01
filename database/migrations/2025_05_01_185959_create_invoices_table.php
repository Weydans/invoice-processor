<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\InvoiceStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('number', 14);
            $table->date('issue_date');
            $table->decimal('amount_paid', total: 8, places: 2);
            $table->enum('status', array_column(InvoiceStatus::cases(), 'value'))->default(InvoiceStatus::PENDING->value);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
