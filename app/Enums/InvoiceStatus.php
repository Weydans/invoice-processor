<?php

namespace App\Enums;

enum InvoiceStatus: int
{
    case PENDING = 1;
    case PARTIALLY_PAID = 2;
    case PAID = 3;
}
