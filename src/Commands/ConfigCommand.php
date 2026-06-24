<?php

namespace ErrorLoop\Cli\Commands;

use ErrorLoop\Cli\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'config', description: 'Configure the ErrorLoop CLI')]
class ConfigCommand extends Command
{
    public function __construct(private Config $config)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('endpoint', null, InputOption::VALUE_OPTIONAL, 'Set the ErrorLoop API endpoint');
        $this->addOption('token', null, InputOption::VALUE_OPTIONAL, 'Set the agent token');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $endpoint = $input->getOption('endpoint');
        $token = $input->getOption('token');

        if ($endpoint !== null) {
            $this->config->setEndpoint($endpoint);
            $output->writeln('<info>Endpoint updated.</info>');
        }

        if ($token !== null) {
            $this->config->setAgentToken($token);
            $output->writeln('<info>Agent token updated.</info>');
        }

        $output->writeln('');
        $output->writeln('<comment>Current config:</comment>');
        $output->writeln('  Endpoint:    '.$this->config->getEndpoint());
        $output->writeln('  Agent token: '.$this->maskToken($this->config->getAgentToken()));

        return Command::SUCCESS;
    }

    private function maskToken(string $token): string
    {
        if ($token === '') {
            return '<not set>';
        }

        if (strlen($token) <= 8) {
            return str_repeat('*', strlen($token));
        }

        return substr($token, 0, 4).'...'.substr($token, -4);
    }
}
