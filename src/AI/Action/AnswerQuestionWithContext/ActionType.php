<?php

namespace App\AI\Action\AnswerQuestionWithContext;

use App\AI\Action\DataType\TextWithContext;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\ActionInterface;
use Ibexa\Contracts\ConnectorAi\ActionType\ActionTypeInterface;
use Ibexa\Contracts\ConnectorAi\DataType;
use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

final class ActionType implements ActionTypeInterface
{
    public const IDENTIFIER = 'answer_question_with_context';

    public function __construct(
        private readonly iterable $actionHandlers
    ) {
    }

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function getName(): string
    {
        return 'Answer Question with Context';
    }

    public function getInputIdentifier(): string
    {
        return TextWithContext::getIdentifier();
    }

    public function getOutputIdentifier(): string
    {
        return Text::getIdentifier();
    }

    public function getOptions(): array
    {
        return [];
    }

    public function createAction(DataType $input, array $parameters = []): ActionInterface
    {
        if (!$input instanceof TextWithContext) {
            throw new InvalidArgumentException(
                'input',
                'expected \Ibexa\Contracts\ConnectorAi\Action\DataType\Text type, ' . get_debug_type($input) . ' given.'
            );
        }

        return new Action($input);
    }

    public function getActionHandlers(): iterable
    {
        return $this->actionHandlers;
    }
}
