<?php

declare(strict_types=1);

namespace App\Command;

use App\AI\Action\TextToEmbeddings\Action as TextToEmbeddingsAction;
use App\AI\Embeddings\Embeddings;
use App\AI\Embeddings\VectorStoreInterface;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\ActionConfigurationServiceInterface;
use Ibexa\Contracts\ConnectorAi\ActionServiceInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchEmbeddingsAICommand extends Command
{
    protected static $defaultName = 'ai:embeddings:search';

    public function __construct(
        private ActionServiceInterface $actionService,
        private ActionConfigurationServiceInterface $actionConfigurationService,
        private PermissionResolver $permissionResolver,
        private UserService $userService,
        private VectorStoreInterface $vectorStore
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('text', InputArgument::REQUIRED);
        $this->addArgument('limit', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('SemanticSearch with AI');
        $this->setUser('admin');
        $text = $input->getArgument('text');
        $limit = (int) $input->getArgument('limit');

        $actionConfiguration = $this->actionConfigurationService->getActionConfiguration('add_text_to_embeding');
        $action = new TextToEmbeddingsAction(new Text([$text]));

        $responseObject = $this->actionService->execute($action, $actionConfiguration);
        $embeddings = $responseObject->getOutput()->getList();

        $embeddingsToSearch = [];
        foreach ($embeddings as $embedding) {
            $embeddingsToSearch[] = new Embeddings($embedding['text'], $embedding['embeddings']);
        }
        $io->info('Top ' . $limit . ' results for phrase: ' . $text);
        $hits = $this->vectorStore->searchForContent($embeddingsToSearch, 'eng-GB', $limit);

        foreach ($hits as $i => $hit) {
            $io->section(($i+1) . ': ' . $hit->getContent()->getContentInfo()->getName());
            $io->writeln('Content ID: ' . $hit->getContent()->getContentInfo()->getId());
        }

        return Command::SUCCESS;
    }

    private function setUser(string $userLogin): void
    {
        $this->permissionResolver->setCurrentUserReference($this->userService->loadUserByLogin($userLogin));
    }
}
