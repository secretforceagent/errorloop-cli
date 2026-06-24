<?php

use ErrorLoop\Cli\Config;

it('reads and writes config', function () {
    $path = sys_get_temp_dir().'/errorloop-config-'.uniqid().'.json';
    $config = new Config($path);

    expect($config->getEndpoint())->toBe('https://errorloop.example.com');
    expect($config->getAgentToken())->toBe('');

    $config->setEndpoint('https://er.ma.rs');
    $config->setAgentToken('secret-token');

    expect($config->getEndpoint())->toBe('https://er.ma.rs');
    expect($config->getAgentToken())->toBe('secret-token');

    $reloaded = new Config($path);
    expect($reloaded->getEndpoint())->toBe('https://er.ma.rs');
    expect($reloaded->getAgentToken())->toBe('secret-token');

    unlink($path);
});
