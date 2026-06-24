<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\ErrorLoopApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'create-project', description: 'Create a new project and print its API key')]
class CreateProjectCommand extends Command
{
    public function __construct(private ErrorLoopApi $api)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Project name');
        $this->addOption('api-key', null, InputOption::VALUE_OPTIONAL, 'Optional API key (a random key is generated if omitted)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $apiKey = $input->getOption('api-key') ?: null;

        $data = ['name' => $name];

        if ($apiKey !== null) {
            $data['api_key'] = $apiKey;
        }

        try {
            $project = $this->api->createProject($data);
        } catch (\Exception $e) {
            $output->writeln('<error>Failed to create project: '.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Project created.</info>');
        $output->writeln('  ID:      '.$project['id']);
        $output->writeln('  Name:    '.$project['name']);
        $output->writeln('  API key: '.$project['api_key']);

        return Command::SUCCESS;
    }
}
