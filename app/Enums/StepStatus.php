<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StepStatus: string implements HasLabel, HasColor
{
    case AFaire = 'a_faire';
    case EnCours = 'en_cours';
    case Termine = 'termine';

    public function getLabel(): string{
        return match ($this) {
            self::AFaire => 'À venir',
            self::EnCours => 'En cours',
            self::Termine => 'Terminée',
        };
    }

    public function getColor(): string{
        return match ($this) {
            self::AFaire => 'gray',
            self::EnCours => 'warning',
            self::Termine => 'success',
        };
    }
}