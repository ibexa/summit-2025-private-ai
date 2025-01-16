<?php

namespace App\AI\Action\DataType;

use Ibexa\Contracts\ConnectorAi\DataType;

class TextWithContext implements DataType
{
    public const IDENTIFIER = 'text_with_context';

    /**
     * @param array{text: string[], context: string} $data
     */
    public function __construct(
        private array $data
    ) {
    }

    /**
     * @return array{text: string, context: string}
     */
    public function getList(): array
    {
        return $this->data;
    }

    public static function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function getTextWithContext(): array
    {
        return reset($this->data);
    }
}
