<?php

declare(strict_types=1);

namespace App\AI\Handler;

use Ibexa\Contracts\ConnectorAi\Action\ActionHandlerInterface;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\Action\Response\TextResponse;
use Ibexa\Contracts\ConnectorAi\ActionInterface;
use Ibexa\Contracts\ConnectorAi\ActionResponseInterface;
use Ibexa\Contracts\ConnectorAi\Action\TextToText\Action as TextToTextAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaTextToTextActionHandler implements ActionHandlerInterface
{
    public const IDENTIFIER = 'OllamaTextToText';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $host = 'http://localhost:11434/api'
    ) {
    }

    public function supports(ActionInterface $action): bool
    {
        return $action instanceof TextToTextAction;
    }

    public function handle(ActionInterface $action, array $context = []): ActionResponseInterface
    {
        $input = $action->getInput();
        $text = $this->sanitizeInput($input->getText());

        $systemMessage = $action->hasActionContext()
            ? $action->getActionContext()->getActionHandlerOptions()->get('system_prompt', '')
            : '';
        $model = $action->hasActionContext()
            ? $action->getActionContext()->getActionHandlerOptions()->get('llm', 'llama3.2:3b')
            : 'llama3.2:3b';

        $response = $this->client->request(
            Request::METHOD_POST,
            sprintf('%s/generate', $this->host),
            [
                'json' => [
                    'model' => $model,
                    'system' => $systemMessage,
                    'prompt' => $text,
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
