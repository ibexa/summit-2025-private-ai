<?php

declare(strict_types=1);

namespace App\Command;

use App\AI\Action\AnswerQuestionWithContext\Action as AnswerQuestionWithContextAction;
use App\AI\Action\DataType\TextWithContext;
use App\AI\Action\TextToEmbeddings\Action as TextToEmbeddingsAction;
use App\AI\Embeddings\Embeddings;
use App\AI\Embeddings\VectorStoreInterface;
use Ibexa\Contracts\ConnectorAi\Action\DataType\Text;
use Ibexa\Contracts\ConnectorAi\Action\RuntimeContext;
use Ibexa\Contracts\ConnectorAi\ActionConfigurationServiceInterface;
use Ibexa\Contracts\ConnectorAi\ActionServiceInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AnswerQuestionWithContextAICommand extends Command
{
    protected static $defaultName = 'ai:embeddings:question';

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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("AI: Answer Question with Context");
        $this->setUser('admin');
        $text = $input->getArgument('text');

        $actionConfiguration = $this->actionConfigurationService->getActionConfiguration(
            'add_text_to_embeding'
        );
        $action = new TextToEmbeddingsAction(new Text([$text]));
        $responseObject = $this->actionService->execute($action, $actionConfiguration);
        $embeddingsResponse = $responseObject->getOutput()->getList();
        $embeddings = [];
        foreach ($embeddingsResponse as $embeddingsData) {
            $embeddings[] = new Embeddings($embeddingsData['text'], $embeddingsData['embeddings']);
        }

        $hits = $this->vectorStore->searchForChunks($embeddings, null);

        $context = '';
        foreach ($hits as $hit) {
            $context .= $hit->getText() . ' ';
        }

        $answerQuestionActionConfiguration = $this->actionConfigurationService->getActionConfiguration(
            'answer_using_context_from_blog_post'
        );
        $answerQuestionAction = new AnswerQuestionWithContextAction(
            new TextWithContext([
                [
                    'text' => $text, 'context' => $context
                ],
            ])
        );
        $responseObject = $this->actionService->execute($answerQuestionAction, $answerQuestionActionConfiguration);
        $responseText = $responseObject->getOutput()->getList()[0];

        $output->writeln($responseText);

        return Command::SUCCESS;
    }

    private function setUser(string $userLogin): void
    {
        $this->permissionResolver->setCurrentUserReference($this->userService->loadUserByLogin($userLogin));
    }
}
