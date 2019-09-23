<?php

namespace App\Twig;

use App\Service\MarkdownCacheHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    protected $helper;

    public function __construct(MarkdownCacheHelper $helper)
    {
        $this->helper = $helper;
    }
    public function getFilters()
    {
        // 'is_safe' precise que c'est sécurisé
        return [
            new TwigFilter('markdown', [$this, 'parseMarkdown'], ['is_safe' => ['html']])
        ];
    }

    public function parseMarkdown($content)
    {
        // utilisation du filtre dans la vue: {{ product.description | markdown }}
        return $this->helper->parse($content);
    }
}
