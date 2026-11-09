<?php

namespace App\Enums;

enum ClientType: string
{
    case Particulier = 'particulier';
    case Entreprise = 'entreprise';
}