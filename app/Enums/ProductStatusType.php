<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProductStatusType extends Enum
{
    public const AVAILABLE = 1;
    public const UNAVAILABLE = 0;
    public const HIDDEN = 10;
}
