<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class InvoiceStatusType extends Enum
{
    // Ahtung! Range of -128 to 127
    public const OPEN = 0;
    public const AWAITING_PAYMENT = 10;
    public const PROCESSING_PAYMENT = 20;
    public const PAID = 30;
    public const REJECTED = 40;
    public const CANCELED = 100;
    public const REFUNDED = 50;
}
