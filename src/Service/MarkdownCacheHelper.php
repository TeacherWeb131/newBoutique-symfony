<?php

namespace App\Service;

use cebe\markdown\Markdown;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MarkdownCacheHelper
{
    protected $parser;
    protected $cache;

    public function __construct(Markdown $parser, CacheInterface $cache)
    {
        $this->parser = $parser;
        $this->cache = $cache;
    }

    public function parse($content): string
    {
        // Rappel: avec use(), j'injecte les service dont la fonction a besoin car dans la fonction il ne sont pas connus
        return $this->cache->get(md5($content), function (ItemInterface $item) use ($content) {
            return $this->parser->parse($content);
        });
    }
}
