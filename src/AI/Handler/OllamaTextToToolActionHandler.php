<?php

declare(strict_types=1);

namespace App\AI\Handler;

use App\AI\Action\TextWithFunction\Action as TextWithFunctionAction;
use Ibexa\Contracts\ConnectorAi\Action\ActionHandlerInterface;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\Action\Response\TextResponse;
use Ibexa\Contracts\ConnectorAi\ActionInterface;
use Ibexa\Contracts\ConnectorAi\ActionResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaTextToToolActionHandler implements ActionHandlerInterface
{
    public const IDENTIFIER = 'OllamaTextToToolActionHandler';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $model = 'llama3.2:3b',
        private readonly string $host = 'http://localhost:11434/api'
    ) {
    }

    public function supports(ActionInterface $action): bool
    {
        return $action instanceof TextWithFunctionAction;
    }

    public function handle(ActionInterface $action, array $context = []): ActionResponseInterface
    {
        $input = $action->getInput();
        $text = $this->sanitizeInput($input->getText());
        $options = $action->getActionContext()->getActionHandlerOptions();
        $baseJsonRequestContent = [
            'model' => $this->model,
            'system' => $options['system_prompt'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $text,
                ],
            ],
            'stream' => false,
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => $options['name'],
                        'description' => $options['description'],
                        'properties' => [
                            $options['parameter_name'] => [
                                'type' => 'string',
                                'description' => $options['parameter_description']
                            ],
                        ],
                        'required' => [$options['parameter_name']],
                    ],
                ],
            ],
        ];

        $requestJson = $baseJsonRequestContent;
        $requestJson['messages'] = [
            [
                'role' => 'user',
                'content' => $text,
            ],
        ];
        $response = $this->client->request(
            Request::METHOD_POST,
            sprintf('%s/chat', $this->host),
            [
                'json' => $requestJson,
            ]
        );

        $message = json_decode($response->getContent(), true)['message'];
        if (isset($message['tool_calls']) && count($message['tool_calls']) > 0) {
            dump($message['tool_calls']);
            $function = $message['tool_calls'][0]['function'];
            if (!empty($function['arguments']['city'])) {
                $functionName = $function['name'];
                $requestJson = $baseJsonRequestContent;
                $requestJson['messages'] = [
                    [
                        'role' => 'user',
                        'content' => $text,
                    ],
                    [
                        'role' => 'tool',
                        'content' => $this->$functionName($function['arguments']['city']),
                    ],
                ];
            } else {
                unset($requestJson['tools']);
            }

            $response = $this->client->request(
                Request::METHOD_POST,
                sprintf('%s/chat', $this->host),
                [
                    'json' => $requestJson,
                ]
            );

            $message = json_decode($response->getContent(), true)['message'];
        }

        return new TextResponse(new Text([$message['content']]));
    }

    public static function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    private function getWeather(string $city): string
    {
        $weatherConditions = ['Rain', 'Storm', 'Cloudy', 'Sunny'];
        $responseText = sprintf('%s, %d degrees Celsius', $weatherConditions[rand(0,3)], rand(10,30));
        dump('getWeather: ' . $responseText);

        return $responseText;
    }

    private function sanitizeInput(string $text): string
    {
        return str_replace(["\n", "\r"], ' ', $text);
    }
}
