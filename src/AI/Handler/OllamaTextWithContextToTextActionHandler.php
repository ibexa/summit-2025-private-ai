<?php

declare(strict_types=1);

namespace App\AI\Handler;

use App\AI\Action\AnswerQuestionWithContext\Action as AnswerQuestionWithContextAction;
use Ibexa\Contracts\ConnectorAi\Action\ActionHandlerInterface;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\Action\Response\TextResponse;
use Ibexa\Contracts\ConnectorAi\ActionInterface;
use Ibexa\Contracts\ConnectorAi\ActionResponseInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaTextWithContextToTextActionHandler implements ActionHandlerInterface
{
    public const IDENTIFIER = 'OllamaTextWithContextToText';
    public const CONTEXT_PATTERN = '{context}';
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $model = 'llama3.2:3b',
        private readonly string $host = 'http://localhost:11434/api'
    ) {
    }

    public function supports(ActionInterface $action): bool
    {
        return $action instanceof AnswerQuestionWithContextAction;
    }

    public function handle(ActionInterface $action, array $context = []): ActionResponseInterface
    {
        $input = $action->getInput();
        $textWithContext = $input->getTextWithContext();

        $systemMessage = $action->getActionContext()->getActionHandlerOptions()->get('system_prompt', '');
        if (!str_contains($systemMessage, self::CONTEXT_PATTERN)) {
            throw new InvalidArgumentException(
                'system_prompt',
                'System prompt must contain context pattern: ' . self::CONTEXT_PATTERN
            );
        }
        $systemMessageWithContext = str_replace(
            self::CONTEXT_PATTERN,
            $textWithContext['context'],
            $systemMessage
        );

        $response = $this->client->request(
            Request::METHOD_POST,
            sprintf('%s/generate', $this->host),
            [
                'json' => [
                    'model' => $this->model,
                    'system' => $systemMessageWithContext,
                    'prompt' => $this->sanitizeInput($textWithContext['text']),
                    'stream' => false,
                ]
            ]
        );

        $output = strip_tags(json_decode($response->getContent(), true)['response']);

        return new TextResponse(new Text([$output]));
    }

    public static function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    private function sanitizeInput(string $text): string
    {
        return str_replace(["\n", "\r"], ' ', $text);
    }
}
