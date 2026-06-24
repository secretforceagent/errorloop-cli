<?php

namespace ErrorLoop\Cli;

use ErrorLoop\Cli\Commands\ClaimCommand;
use ErrorLoop\Cli\Commands\ConfigCommand;
use ErrorLoop\Cli\Commands\CreateProjectCommand;
use ErrorLoop\Cli\Commands\DeployCommand;
use ErrorLoop\Cli\Commands\FixAttemptedCommand;
use ErrorLoop\Cli\Commands\IssueCommand;
use ErrorLoop\Cli\Commands\IssuesCommand;
use ErrorLoop\Cli\Commands\ProjectsCommand;
use ErrorLoop\Cli\Commands\VerifyCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application
{
    public static function run(): int
    {
        $config = new Config;

        $endpoint = getenv('ERRORLOOP_ENDPOINT') ?: $config->getEndpoint();
        $agentToken = getenv('ERRORLOOP_AGENT_TOKEN') ?: $config->getAgentToken();

        $api = new ErrorLoopApi($endpoint, $agentToken);

        $app = new SymfonyApplication('errorloop', '1.0.0');
        $app->addCommand(new IssuesCommand($api));
        $app->addCommand(new IssueCommand($api));
        $app->addCommand(new ProjectsCommand($api));
        $app->addCommand(new ClaimCommand($api));
        $app->addCommand(new FixAttemptedCommand($api));
        $app->addCommand(new DeployCommand($api));
        $app->addCommand(new VerifyCommand($api));
        $app->addCommand(new CreateProjectCommand($api));
        $app->addCommand(new ConfigCommand($config));
        $app->setDefaultCommand('list');

        return $app->run();
    }
}
