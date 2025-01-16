<?php

namespace App\AI\Embeddings;

class Embeddings
{
    /**
     * @param array<float[]> $embeddings
     */
    public function __construct(
        private readonly string $text,
        private readonly array $embeddings
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getEmbeddings(): array
    {
        return $this->embeddings;
    }
}
