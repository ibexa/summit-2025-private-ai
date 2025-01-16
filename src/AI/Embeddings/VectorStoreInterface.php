<?php

namespace App\AI\Embeddings;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

interface VectorStoreInterface
{
    /**
     * @param \App\AI\Embeddings\Embeddings[] $embeddings
     */
    public function storeContentEmbeddings(Content $content, array $embeddings, string $languageCode): void;

    public function removeContentEmbeddings(Content $content, ?string $languageCode): void;

    /**
     * @param \App\AI\Embeddings\Embeddings[] $embeddings
     * @return \App\AI\SemanticSearch\SearchHit[]
     */
    public function searchForChunks(array $embeddings = [], ?string $languageCode = 'eng-GB', int $limit = 3): array;

    /**
     * @param \App\AI\Embeddings\Embeddings[] $embeddings
     * @return \App\AI\SemanticSearch\SearchHit[]
     */
    public function searchForContent(array $embeddings = [], ?string $languageCode = 'eng-GB', int $limit = 3): array;
}
