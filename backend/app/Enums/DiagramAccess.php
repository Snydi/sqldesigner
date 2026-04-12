<?php

namespace App\Enums;

enum DiagramAccess: string
{
    case Read    = 'read';
    case Write   = 'write';
    case Revoked = 'revoked';
}
