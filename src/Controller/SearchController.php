<?php

namespace App\Controller;

use App\AI\Action\TextToEmbeddings\Action as TextToEmbeddingsAction;
use App\AI\Embeddings\Embeddings;
use App\AI\Embeddings\VectorStoreInterface;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\ActionConfigurationServiceInterface;
use Ibexa\Contracts\ConnectorAi\ActionServiceInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="ai_search")
     */
    public function search(
        Request $request,
        ActionServiceInterface $actionService,
        ActionConfigurationServiceInterface $actionConfigurationService,
        PermissionResolver $permissionResolver,
        UserService $userService,
        VectorStoreInterface $vectorStore,
        SearchService $searchService,
    ): Response
    {
        // Only for presentation purposes, actually a bad idea
        $query = $request->get('query', '');
        if (empty($query)) {
            return $this->render('search.html.twig', [
                'query' => $query,
                'keywordHits' => [],
                'semanticHits' => [],
            ]);
        }

        $permissionResolver->setCurrentUserReference($userService->loadUserByLogin('admin'));

        // Semantic Search
        $actionConfiguration = $actionConfigurationService->getActionConfiguration('add_text_to_embeding');
        $action = new TextToEmbeddingsAction(new Text([$query]));
        $responseObject = $actionService->execute($action, $actionConfiguration);
        $embeddings = $responseObject->getOutput()->getList();
        $embeddingsToSearch = [];
        foreach ($embeddings as $embedding) {
            $embeddingsToSearch[] = new Embeddings($embedding['text'], $embedding['embeddings']);
        }
        $semanticHits = $vectorStore->searchForContent($embeddingsToSearch, 'eng-GB', 3);

        // Keyword search
        $locationQuery = new LocationQuery();
        $locationQuery->filter = new Criterion\ContentTypeIdentifier('blog_post');
        $locationQuery->query = new Criterion\FullText($query);
        $locationQuery->limit = 3;
        $keywordHits = $searchService->findContent($locationQuery);

        return $this->render('search.html.twig', [
            'query' => $query,
            'keywordHits' => $keywordHits->searchHits,
            'semanticHits' => $semanticHits,
        ]);

    }
}
