<?php

namespace App\AI\SemanticSearch;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class SearchHit
{
    public function __construct(
        private readonly string $text,
        private readonly ?Content $content = null
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }
}