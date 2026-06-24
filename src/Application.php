<?php

namespace ErrorLoop\Cli;

use ErrorLoop\Cli\Commands\ClaimCommand;
use ErrorLoop\Cli\Commands\DeployCommand;
use ErrorLoop\Cli\Commands\FixAttemptedCommand;
use ErrorLoop\Cli\Commands\IssueCommand;
use ErrorLoop\Cli\Commands\IssuesCommand;
use ErrorLoop\Cli\Commands\VerifyCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application
{
    public static function run(): int
    {
        $endpoint = getenv('ERRORLOOP_ENDPOINT') ?: 'https://errorloop.example.com';
        $agentToken = getenv('ERRORLOOP_AGENT_TOKEN') ?: '';

        $api = new ErrorLoopApi($endpoint, $agentToken);

        $app = new SymfonyApplication('errorloop', '1.0.0');
        $app->addCommand(new IssuesCommand($api));
        $app->addCommand(new IssueCommand($api));
        $app->addCommand(new ClaimCommand($api));
        $app->addCommand(new FixAttemptedCommand($api));
        $app->addCommand(new DeployCommand($api));
        $app->addCommand(new VerifyCommand($api));
        $app->setDefaultCommand('list');

        return $app->run();
    }
}
