<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ShopifyProductStatusType extends Enum
{
    public const PENDING = 0;
    public const OK = 1;
    public const FAIL = 2;
}
