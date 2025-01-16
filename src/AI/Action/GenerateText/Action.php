<?php

declare(strict_types=1);

namespace App\AI\Action\GenerateText;

use Ibexa\Contracts\ConnectorAi\Action\TextToText\Action as BaseTextToTextAction;

final class Action extends BaseTextToTextAction
{
    public function getActionTypeIdentifier(): string
    {
        return 'generate_text';
    }
}
