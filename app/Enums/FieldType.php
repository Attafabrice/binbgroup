<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FieldType: string implements HasLabel
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Number = 'number';
    case Select = 'select';
    case Date = 'date';
    case File = 'file';

    public function getLabel(): string
    {
        return match ($this) {
            self::Text => 'Texte court',
            self::Textarea => 'Texte long',
            self::Number => 'Nombre',
            self::Select => 'Liste de choix',
            self::Date => 'Date',
            self::File => 'Fichier à téléverser',
        };
    }
}