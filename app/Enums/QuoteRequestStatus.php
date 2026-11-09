<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QuoteRequestStatus: string implements HasLabel, HasColor
{
    case Nouvelle = 'nouvelle';
    case EnCours = 'en_cours';
    case Acceptee = 'acceptee';
    case DevisEnvoye = 'devis_envoye';
    case Convertie = 'convertie';
    case Rejetee = 'rejetee';

    public function getLabel(): string{
        return match ($this) {
            self::Nouvelle => 'Reçue',
            self::EnCours => 'En étude',
            self::Acceptee => 'Validée',
            self::DevisEnvoye => 'Devis envoyé',
            self::Convertie => 'Projet lancé',
            self::Rejetee => 'Refusée',
        };
    }

    public function getColor(): string{
        return match ($this) {
            self::Nouvelle => 'info',
            self::EnCours => 'warning',
            self::Acceptee => 'success',
            self::DevisEnvoye => 'primary',
            self::Convertie => 'success',
            self::Rejetee => 'danger',
        };
    }
}