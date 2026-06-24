<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\ErrorLoopApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'projects', description: 'List projects')]
class ProjectsCommand extends Command
{
    public function __construct(private ErrorLoopApi $api)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->api->getProjects();
        $projects = $response['data'] ?? [];

        if (empty($projects)) {
            $output->writeln('<info>No projects found.</info>');

            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['ID', 'Name', 'Created At']);

        foreach ($projects as $project) {
            $table->addRow([
                $project['id'],
                $project['name'],
                $project['created_at'] ?? 'N/A',
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
