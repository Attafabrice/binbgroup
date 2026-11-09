<?php

namespace App\Enums;

enum DocumentOrigin: string
{
    case Client = 'client';
    case Staff = 'staff';
}