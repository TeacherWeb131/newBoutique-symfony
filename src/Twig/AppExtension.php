<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    // RAPPEL: 3 façons d'écrire un Callable (une fonction qu'on peut appeler) :
    // - fonction anonyme
    // - tableau avec instance d'objet et nom de méthode
    // - nom d'une fonction
    public function getFilters()
    {
        return [
            new TwigFilter("price", [$this, "formatPrice"])
        ];
    }

    // {{ 1125.5 | price }} => 1125,50 €
    public function formatPrice($value)
    {
        $final = number_format($value, 2, ",", " ");
        return $final . " €";
    }
}
