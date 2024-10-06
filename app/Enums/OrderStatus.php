<?php
/* Borozepped */

namespace App\Enums;

enum OrderStatus: string
{
    case Unpaid     = 'unpaid';
    case Paid       = 'paid';
    case Cancelled  = 'cancelled';
    case Shipped    = 'shipped';
    case Completed  = 'completed';

    public static function getStatuses(): array
    {
        return [
            self::Paid,
            self::Unpaid,
            self::Cancelled,
            self::Shipped,
            self::Completed,
        ];
    }
}
