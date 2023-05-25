<?php

namespace App\Enums;

enum Status: string
{
    case PENDING = 'P';
    case APRROVED = 'A';
    case CANCELED = 'C';
}
