<?php

namespace App\AI\Embeddings;

use App\AI\SemanticSearch\SearchHit;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use PgSql\Connection;
use Pgvector\Vector;

class PsVectorStore implements VectorStoreInterface
{
    private static ?Connection $connection = null;

    public function __construct(
        string $connectionString,
        private readonly ContentService $contentService,
    ) {
        if (!self::$connection) {
            self::$connection = pg_connect($connectionString);
        }
    }

    /**
     * @param Content $content
     * @param \App\AI\Embeddings\Embeddings[] $embeddings
     * @param string $languageCode
     * @return void
     */
    public function storeContentEmbeddings(Content $content, array $embeddings, string $languageCode = 'eng-GB'): void
    {
        $this->removeContentEmbeddings($content, $languageCode);

        for ($chunk = 0; $chunk < count($embeddings); $chunk++) {
            $vector = new Vector($embeddings[$chunk]->getEmbeddings());
            $contentId = $content->contentInfo->id;
            $identifier = sprintf('content-%d-%d', $contentId, $chunk);

            pg_query_params(
                self::$connection,
                'INSERT INTO items (content_id, identifier, language_code, text, embedding) VALUES ($1, $2, $3, $4, $5)',
                [$contentId, $identifier, $languageCode, $embeddings[$chunk]->getText(), $vector]
            );
        }
    }

    public function removeContentEmbeddings(Content $content, ?string $languageCode = null): void
    {
        $query = 'DELETE FROM items WHERE content_id = $1';
        $params = [
            $content->contentInfo->id,
        ];
        if ($languageCode) {
            $query .= ' AND language_code = $2';
            $params[] = $languageCode;
        }

        pg_query_params(self::$connection, $query, $params);
    }

    public function searchForChunks(array $embeddings = [], ?string $languageCode = 'eng-GB', int $limit = 3): array
    {
        $searchHits = [];
        $languageCodeParam = '%';
        if ($languageCode !== null) {
            $languageCodeParam = $languageCode;
        }

        foreach ($embeddings as $embedding) {
            $vector = new Vector($embedding->getEmbeddings());
            $result = pg_query_params(
                self::$connection,
                "SELECT * FROM items WHERE language_code LIKE $2 ORDER BY embedding <-> $1 LIMIT $3",
                [$vector, $languageCodeParam, $limit]
            );

            while ($row = pg_fetch_array($result)) {
                $searchHits[] = new SearchHit($row['text'], null);
            }

            pg_free_result($result);
        }

        return $searchHits;
    }

    public function searchForContent(array $embeddings = [], ?string $languageCode = 'eng-GB', int $limit = 3): array
    {
        $searchHits = [];
        $contentHits = [];
        $languageCodeParam = '%';
        $contentLanguages = null;
        if ($languageCode !== null) {
            $languageCodeParam = $languageCode;
            $contentLanguages = [$languageCode];
        }

        foreach ($embeddings as $embedding) {
            $vector = new Vector($embedding->getEmbeddings());
            $result = pg_query_params(
                self::$connection,
                "SELECT content_id FROM items WHERE language_code LIKE $2 " .
                "ORDER BY embedding <-> $1 LIMIT 100",
                [$vector, $languageCodeParam]
            );

            while ($row = pg_fetch_array($result)) {
                $contentId = $row['content_id'];
                if (array_key_exists($contentId, $contentHits)) {
                    continue;
                }
                $contentHits[$contentId] = $this->contentService->loadContent($contentId, $contentLanguages);
                $searchHits[] = new SearchHit('', $contentHits[$contentId]);
                if (count($searchHits) >= $limit) {
                    break;
                }
            }

            pg_free_result($result);
        }

        return $searchHits;
    }
}
