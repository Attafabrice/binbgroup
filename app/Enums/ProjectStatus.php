<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasLabel, HasColor
{
    case Planifie = 'planifie';
    case EnCours = 'en_cours';
    case Suspendu = 'suspendu';
    case Termine = 'termine';
    case Annule = 'annule';

    public function getLabel(): string{
        return match ($this) {
            self::Planifie => 'Planifié',
            self::EnCours => 'En cours',
            self::Suspendu => 'Suspendu',
            self::Termine => 'Terminé',
            self::Annule => 'Annulé',
        };
    }

    public function getColor(): string{
        return match ($this) {
            self::Planifie => 'gray',
            self::EnCours => 'warning',
            self::Suspendu => 'danger',
            self::Termine => 'success',
            self::Annule => 'gray',
        };
    }
}