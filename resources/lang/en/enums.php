<?php

use App\Enums\OrderStatusType;

return [

    OrderStatusType::class => [
        OrderStatusType::PAUSED => 'Paused',
        OrderStatusType::REJECTED_INVOICE => 'Rejected invoice exists',
    ],

];
