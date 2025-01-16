<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\AI\Action\TextToEmbeddings\Action as TextToEmbeddingsAction;
use App\AI\Embeddings\Embeddings;
use App\AI\Embeddings\VectorStoreInterface;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\Action\RuntimeContext;
use Ibexa\Contracts\ConnectorAi\ActionConfigurationServiceInterface;
use Ibexa\Contracts\ConnectorAi\ActionServiceInterface;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter;
use Langchain\TextSplitter\RecursiveCharacterTextSplitter;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BlogPostAIEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ActionConfigurationServiceInterface $actionConfigurationService,
        private readonly ActionServiceInterface $actionService,
        private readonly Converter $richTextConverter,
        private readonly VectorStoreInterface $vectorStore
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => ['onPublishVersion', 0],
        ];
    }

    public function onPublishVersion(PublishVersionEvent $event): void
    {
        // Lets make embeddings only for Blog Posts
        $content = $event->getContent();
        $contentTypeIdentifier = $content->getContentType()->getIdentifier();
        if ($contentTypeIdentifier !== 'blog_post') {
            return;
        }
        
        $actionConfiguration = $this->actionConfigurationService->getActionConfiguration('add_text_to_embeding');
        $blogPostBody = $content->getField('body')->getValue();
        $htmlBlogPostBody = $this->richTextConverter->convert($blogPostBody->xml)->saveHTML();
        $markdownConverter = new HtmlConverter();
        $markdownBody = $markdownConverter->convert($htmlBlogPostBody);

        $textSplitter = new RecursiveCharacterTextSplitter(['chunk_size' => 900, 'chunk_overlap' => 50]);
        $chunks = $textSplitter->splitText($markdownBody);
        $chunksWithContentName = [];
        foreach ($chunks as $chunk) {
            $chunksWithContentName[] = '#' . $content->getContentInfo()->name . ' \n\n ' . $chunk;
        }
        $action = new TextToEmbeddingsAction(new Text($chunksWithContentName));


        $responseObject = $this->actionService->execute($action, $actionConfiguration);
        $embeddingsList = $responseObject->getOutput()->getList();
        $embeddings = [];
        foreach ($embeddingsList as $embeddingsData) {
            $embeddings[] = new Embeddings($embeddingsData['text'], $embeddingsData['embeddings']);
        }

        $this->vectorStore->storeContentEmbeddings($content, $embeddings, 'eng-GB');
    }
}
