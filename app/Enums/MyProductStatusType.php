<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class MyProductStatusType extends Enum
{
    public const CONNECTED = 10;
    public const NON_CONNECTED = 20;
    public const SHOPIFY_SEND_PENDING = 30;
}
