<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\ErrorLoopApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'verify', description: 'Verify an issue is resolved')]
class VerifyCommand extends Command
{
    public function __construct(private ErrorLoopApi $api)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::OPTIONAL, 'Issue ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument('id');

        if ($id === null) {
            $output->writeln('<comment>Batch verification is handled by the server scheduler.</comment>');

            return Command::SUCCESS;
        }

        $output->writeln(sprintf('<comment>Manual verify for issue #%d is not exposed via API; use errorloop:verify on the server or the scheduler.</comment>', $id));

        return Command::SUCCESS;
    }
}
