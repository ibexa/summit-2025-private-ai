<?php

declare(strict_types=1);

namespace App\AI\Action\TextWithFunction;

use Ibexa\Contracts\ConnectorAi\Action\Action as BaseAction;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\DataType;

final class Action extends BaseAction
{
    public function __construct(
        private readonly Text $data
    ) {
    }

    public function getActionTypeIdentifier(): string
    {
        return 'text_with_tools';
    }

    public function getParameters(): array
    {
        return [];
    }

    public function getInput(): DataType
    {
        return $this->data;
    }
}
