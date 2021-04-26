<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminAccountCommand extends Command
{
    /**
     * @var App\Service\UserService
     */
    private $userService;

    /**
     * @var string Name of the command (used in console)
     */
    protected static $defaultName = 'app:create-admin-account';

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        parent::__construct();
    }

    /**
     * Command name and parameters
     */
    protected function configure()
    {
        $this
                ->setDescription('Create the first admin account.')
                ->addArgument('admin-username', InputArgument::REQUIRED, 'Admin username')
                ->addArgument('admin-password', InputArgument::REQUIRED, 'Admin password')
        ;
    }

    /**
     * Actions executed when running the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Admin account creation started...');

        $this->userService->createFirstAdminAccount(
                $input->getArgument('admin-username'),
                $input->getArgument('admin-password')
        );

        $output->writeln('User successfully generated!');
    }

}
