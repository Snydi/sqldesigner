<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case INITIATED = 'initiated';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
}
