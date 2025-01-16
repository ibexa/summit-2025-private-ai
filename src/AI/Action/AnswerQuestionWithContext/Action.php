<?php

namespace App\AI\Action\AnswerQuestionWithContext;

use App\AI\Action\DataType\TextWithContext;
use Ibexa\Contracts\ConnectorAi\Action\Action as BaseAction;
use Ibexa\Contracts\ConnectorAi\DataType;

class Action extends BaseAction
{
    public function __construct(
        private TextWithContext $text
    ) {
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function getParameters(): array
    {
        return [
            'context' => $this->context,
        ];
    }

    public function getInput(): DataType
    {
        return $this->text;
    }

    public function getActionTypeIdentifier(): string
    {
        return 'answer_question_with_context';
    }
}