<?php

namespace App\Enums;

enum WalletStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PND =  'pnd';
}
