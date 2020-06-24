<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderStatusType extends Enum
{
    public const HOLD = 0; // Cancel is possible
    public const PAUSED = 10; // Possible in HOLD
    public const CHECKING = 15; // Order is checking...
    public const PENDING = 20; // Price was changed, agreement required, any product currently in stock, cannot be delivered, etc
    public const NO_CARD  = 25; // User has any card
    public const CONFIRMED = 30; // Goods can be ordered
    public const SUSPENDED = 35; // Paid but we cannot order goods for some reason
    public const CREATED = 40; // Created @ Ali
    public const PAID1 = 50; // Pain @ Ali
    public const PAID2 = 60; // Payment verification completed
    public const SHIPPED = 70; // Cannot be cancelled
    public const DELIVERED = 80; // Cannot be cancelled

    public const REJECTED_INVOICE = 200; // Linked invoice payment rejected
    public const CANCELLED = 210; // Cancelled
    public const FAILED = 220; // Something bad happened 
    public const TO_REFUND = 230; // Need to refund the money
    public const REFUNDED = 240; // Money refunded

    public static $availableManualTransitions = [
        self::HOLD => [
            'Pause' => self::PAUSED,
            'Cancel' => self::CANCELLED,
            'Confirm' => self::PENDING,
        ],
        self::PAUSED    => [
            'Cancel' => self::CANCELLED,
            'Confirm' => self::PENDING,
        ],
        self::PENDING => [],
        self::NO_CARD => [
            'Confirm' => self::PENDING,
        ],
        self::CONFIRMED => [
            //'Created' => self::CREATED,
            //'Delivered' => self::DELIVERED,
            //'Shipped' => self::SHIPPED,
            //'Failed' => self::FAILED,
            //'Refunded' => self::REFUNDED,
        ],
        self::SUSPENDED => [],
        self::CREATED   => [],
        self::REJECTED_INVOICE  => [
            'Cancel' => self::CANCELLED,
        ],
    ];

    public static function confirmedStatuses(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::CREATED,
            self::PAID1,
            self::PAID2,
            self::SHIPPED,
            self::DELIVERED,
        ];
    }

    public static function getNovaStatusList(): array
    {
        return [
            self::getDescription(OrderStatusType::HOLD) => OrderStatusType::HOLD,
            self::getDescription(OrderStatusType::PAUSED) => OrderStatusType::PAUSED,
            self::getDescription(OrderStatusType::CHECKING) => OrderStatusType::CHECKING,
            self::getDescription(OrderStatusType::PENDING) => OrderStatusType::PENDING,
            self::getDescription(OrderStatusType::NO_CARD) => OrderStatusType::NO_CARD,
            self::getDescription(OrderStatusType::CONFIRMED) => OrderStatusType::CONFIRMED,
            self::getDescription(OrderStatusType::SUSPENDED) => OrderStatusType::SUSPENDED,
            self::getDescription(OrderStatusType::CREATED) => OrderStatusType::CREATED,
            self::getDescription(OrderStatusType::SHIPPED) => OrderStatusType::SHIPPED,
            self::getDescription(OrderStatusType::DELIVERED) => OrderStatusType::DELIVERED,
            self::getDescription(OrderStatusType::REJECTED_INVOICE) => OrderStatusType::REJECTED_INVOICE,
            self::getDescription(OrderStatusType::CANCELLED) => OrderStatusType::CANCELLED,
            self::getDescription(OrderStatusType::FAILED) => OrderStatusType::FAILED,
            self::getDescription(OrderStatusType::REFUNDED) => OrderStatusType::REFUNDED,
        ];
    }

    public static function getUserStatusList(): array
    {
        return [
            self::getKey(OrderStatusType::HOLD) => self::getDescription(OrderStatusType::HOLD),
            self::getKey(OrderStatusType::PAUSED) => self::getDescription(OrderStatusType::PAUSED),
            self::getKey(OrderStatusType::CANCELLED) => self::getDescription(OrderStatusType::CANCELLED),
            self::getKey(OrderStatusType::PENDING) => self::getDescription(OrderStatusType::CONFIRMED),
        ];
    }

    /**
     * @return array
     */
    public static function getAvailableManualTransitions(int $status): array
    {
        if (! isset(self::$availableManualTransitions[$status])) {
            return [];
        }

        $transitions = (array) self::$availableManualTransitions[$status];
        foreach ($transitions as &$transition) {
            $transition = self::getKey($transition);
        }

        return $transitions;
    }
}
