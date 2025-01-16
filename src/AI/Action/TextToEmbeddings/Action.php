<?php

namespace App\AI\Action\TextToEmbeddings;

use Ibexa\Contracts\ConnectorAi\Action\Action as BaseAction;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\DataType;

class Action extends BaseAction
{
    public function __construct(
        private Text $text,
        private int $maxLength = 1000
    ) {
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function setMaxLength(int $maxLength): void
    {
        $this->maxLength = $maxLength;
    }

    public function getParameters(): array
    {
        return [
            'max_length' => $this->maxLength,
        ];
    }

    public function getInput(): DataType
    {
        return $this->text;
    }

    public function getActionTypeIdentifier(): string
    {
        return 'text_to_embeddings';
    }
}