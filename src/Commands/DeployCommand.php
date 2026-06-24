<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\ErrorLoopApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'deploy', description: 'Record a deploy')]
class DeployCommand extends Command
{
    public function __construct(private ErrorLoopApi $api)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('project', null, InputOption::VALUE_REQUIRED, 'Project ID');
        $this->addOption('sha', null, InputOption::VALUE_REQUIRED, 'Release SHA');
        $this->addOption('environment', null, InputOption::VALUE_OPTIONAL, 'Environment', 'production');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $project = $input->getOption('project');
        $sha = $input->getOption('sha');

        if ($project === null || $sha === null) {
            $output->writeln('<error>Both --project and --sha are required.</error>');

            return Command::FAILURE;
        }

        $response = $this->api->recordDeploy([
            'project_id' => (int) $project,
            'release' => $sha,
            'environment' => $input->getOption('environment'),
        ]);

        $output->writeln(sprintf('<info>Deploy recorded: %s</info>', $response['release'] ?? $sha));

        return Command::SUCCESS;
    }
}
