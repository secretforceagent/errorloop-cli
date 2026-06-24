<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\ErrorLoopApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'issues', description: 'List issues')]
class IssuesCommand extends Command
{
    public function __construct(private ErrorLoopApi $api)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('status', null, InputOption::VALUE_OPTIONAL, 'Filter by status');
        $this->addOption('project-id', null, InputOption::VALUE_OPTIONAL, 'Filter by project ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $status = $input->getOption('status') ?: null;
        $projectId = $input->getOption('project-id') ? (int) $input->getOption('project-id') : null;

        $response = $this->api->getIssues($status, $projectId);
        $issues = $response['data'] ?? [];

        if (empty($issues)) {
            $output->writeln('<info>No issues found.</info>');

            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['ID', 'Project', 'Status', 'Events', 'Last Seen', 'Title']);

        foreach ($issues as $issue) {
            $table->addRow([
                $issue['id'],
                $issue['project_name'] ?? 'N/A',
                $issue['status'],
                $issue['event_count'],
                $issue['last_seen_at'] ?? 'N/A',
                str_replace("\n", ' ', mb_strimwidth($issue['title'], 0, 50, '...')),
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
