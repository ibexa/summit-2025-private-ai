<?php

declare(strict_types=1);

namespace App\Command;

use App\AI\Action\TextWithFunction\Action as TextWithFunctionAction;
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

class CurrentWeatherAICommand extends Command
{
    protected static $defaultName = 'ai:weather';

    public function __construct(
        private ActionServiceInterface $actionService,
        private ActionConfigurationServiceInterface $actionConfigurationService,
        private PermissionResolver $permissionResolver,
        private UserService $userService
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
        $io->title('AI: Current Weather');
        $this->setUser('admin');

        $actionConfiguration = $this->actionConfigurationService->getActionConfiguration('get_current_weather');

        $io->text('Sending question to the Ai...');
        $action = new TextWithFunctionAction(new Text([$input->getArgument('text')]));
        $action->setRuntimeContext(
            new RuntimeContext(
                [
                    'languageCode' => 'eng-GB',
                ]
            )
        );

        $responseObject = $this->actionService->execute($action, $actionConfiguration);
        $responseText = $responseObject->getOutput()->getList()[0];

        $io->success($responseText);

        return Command::SUCCESS;
    }

    private function setUser(string $userLogin): void
    {
        $this->permissionResolver->setCurrentUserReference($this->userService->loadUserByLogin($userLogin));
    }
}
