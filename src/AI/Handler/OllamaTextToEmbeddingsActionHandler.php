<?php

declare(strict_types=1);

namespace App\AI\Handler;

use App\AI\Action\DataType\Embeddings;
use App\AI\Action\TextToEmbeddings\Action as TextToEmbeddingsAction;
use App\AI\Action\TextToEmbeddings\ActionResponse;
use Ibexa\Contracts\ConnectorAi\Action\ActionHandlerInterface;
use Ibexa\Contracts\ConnectorAi\ActionInterface;
use Ibexa\Contracts\ConnectorAi\ActionResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OllamaTextToEmbeddingsActionHandler implements ActionHandlerInterface
{
    public const IDENTIFIER = 'TextToEmbeddings';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $model = 'nomic-embed-text',
        private readonly string $host = 'http://localhost:11434/api'
    ) {
    }

    public function supports(ActionInterface $action): bool
    {
        return $action instanceof TextToEmbeddingsAction;
    }

    public function handle(ActionInterface $action, array $context = []): ActionResponseInterface
    {
        $input = $action->getInput();

        $embeddings = [];
        /** @var string $text */
        foreach ($input->getList() as $text) {
            $response = $this->client->request(
                Request::METHOD_POST,
                sprintf('%s/embeddings', $this->host),
                [
                    'json' => [
                        'model' => $this->model,
                        'prompt' => $text,
                    ]
                ]
            );

            $content = json_decode($response->getContent(), true);
            $embeddings[] = [
                'text' => $text,
                'embeddings' => $content['embedding'],
            ];
        }

        return new ActionResponse(new Embeddings($embeddings));
    }

    public static function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
