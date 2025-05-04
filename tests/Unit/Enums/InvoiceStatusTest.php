<?php

namespace Tests\Unit\Enums;

use App\Enums\InvoiceStatus;
use PHPUnit\Framework\TestCase;

class InvoiceStatusTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals(InvoiceStatus::PENDING->value, 1);
        $this->assertEquals(InvoiceStatus::PARTIALLY_PAID->value, 2);
        $this->assertEquals(InvoiceStatus::PAID->value, 3);
    }
}
