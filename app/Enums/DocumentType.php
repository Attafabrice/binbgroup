<?php

namespace App\Enums;

enum DocumentType: string
{
    case Devis = 'devis';
    case Contrat = 'contrat';
    case Plan = 'plan';
    case Rapport = 'rapport';
    case Recu = 'recu';
    case PieceJustificative = 'piece_justificative';
    case Autre = 'autre';
}