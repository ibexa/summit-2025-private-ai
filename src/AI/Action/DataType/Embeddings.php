<?php

declare(strict_types=1);

namespace App\AI\Action\DataType;

use Ibexa\Contracts\ConnectorAi\DataType;

class Embeddings implements DataType
{
    public const IDENTIFIER = 'embeddings';

    /**
     * @param array{text: string, embeddings: array} $data
     */
    public function __construct(
        private array $data
    ) {
    }

    /**
     * @return array{text: string, embeddings: array}
     */
    public function getList(): array
    {
        return $this->data;
    }

    public static function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
