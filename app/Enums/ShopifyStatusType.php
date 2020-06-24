<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ShopifyStatusType extends Enum
{
    public const UNKNOWN = 0;
    public const PENDING = 10;
    public const AUTHORIZED = 20;
    public const PARTIALLY_PAID = 30;
    public const PAID = 40;
    public const PARTIALLY_REFUNDED = 50;
    public const REFUNDED = 60;
    public const VOIDED = 70;
    public const MIGRATED = 80;


    public static $shopifyStatuses = [
        'PENDING'               => self::PENDING,
        'AUTHORIZED'            => self::AUTHORIZED,
        'PARTIALLY_PAID'        => self::PARTIALLY_PAID,
        'PAID'                  => self::PAID,
        'PARTIALLY_REFUNDED'    => self::PARTIALLY_REFUNDED,
        'REFUNDED'              => self::REFUNDED,
        'VOIDED'                => self::VOIDED,
        'MIGRATED'              => self::MIGRATED
    ];
}
