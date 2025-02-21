<?php

namespace App\Enum;

enum OrderStatusEnum : string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}