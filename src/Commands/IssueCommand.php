<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\ErrorLoopApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'issue', description: 'Show a single issue')]
class IssueCommand extends Command
{
    public function __construct(private ErrorLoopApi $api)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Issue ID');
        $this->addOption('for-agent', null, InputOption::VALUE_NONE, 'Output concise agent context');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (int) $input->getArgument('id');
        $issue = $this->api->getIssue($id);

        if (empty($issue) || empty($issue['data'])) {
            $output->writeln('<error>Issue not found.</error>');

            return Command::FAILURE;
        }

        $data = $issue['data'];

        if ($input->getOption('for-agent')) {
            $output->writeln(sprintf('Issue: #%d', $data['id']));
            $output->writeln(sprintf('Project: %s', $data['project_name'] ?? 'N/A'));
            $output->writeln(sprintf('Status: %s', $data['status']));
            $output->writeln(sprintf('Fingerprint: %s', $data['fingerprint']));
            $output->writeln(sprintf('Title: %s', $data['title']));
            $output->writeln(sprintf('Event count: %d', $data['event_count']));
            $output->writeln(sprintf('First seen: %s', $data['first_seen_at'] ?? 'N/A'));
            $output->writeln(sprintf('Last seen: %s', $data['last_seen_at'] ?? 'N/A'));

            return Command::SUCCESS;
        }

        $output->writeln(sprintf('<info>Issue #%d</info>', $data['id']));
        $output->writeln(sprintf('Status: %s', $data['status']));
        $output->writeln(sprintf('Title: %s', $data['title']));
        $output->writeln(sprintf('Events: %d', $data['event_count']));

        return Command::SUCCESS;
    }
}
