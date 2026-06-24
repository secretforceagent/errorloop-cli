<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\ErrorLoopApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'fix-attempted', description: 'Record a fix attempt')]
class FixAttemptedCommand extends Command
{
    public function __construct(private ErrorLoopApi $api)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Issue ID');
        $this->addOption('commit', null, InputOption::VALUE_OPTIONAL, 'Commit SHA');
        $this->addOption('branch', null, InputOption::VALUE_OPTIONAL, 'Branch');
        $this->addOption('agent', null, InputOption::VALUE_OPTIONAL, 'Agent name', get_current_user());
        $this->addOption('notes', null, InputOption::VALUE_OPTIONAL, 'Notes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (int) $input->getArgument('id');

        $data = array_filter([
            'agent' => $input->getOption('agent'),
            'commit_sha' => $input->getOption('commit'),
            'branch' => $input->getOption('branch'),
            'notes' => $input->getOption('notes'),
        ], fn ($value) => $value !== null);

        $response = $this->api->recordFixAttempt($id, $data);

        $output->writeln(sprintf('<info>Fix attempt recorded for issue #%d. Status: %s</info>', $id, $response['status'] ?? 'unknown'));

        return Command::SUCCESS;
    }
}
