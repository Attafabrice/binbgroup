<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QuoteStatus: string implements HasLabel, HasColor{
    case Brouillon = 'brouillon';
    case Envoye = 'envoye';
    case Accepte = 'accepte';
    case Refuse = 'refuse';
    case Expire = 'expire';

    public function getLabel(): string{
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Envoye => 'Envoyé au client',
            self::Accepte => 'Accepté',
            self::Refuse => 'Refusé',
            self::Expire => 'Expiré',
        };
    }

    public function getColor(): string{
        return match ($this) {
            self::Brouillon => 'gray',
            self::Envoye => 'primary',
            self::Accepte => 'success',
            self::Refuse => 'danger',
            self::Expire => 'warning',
        };
    }
}