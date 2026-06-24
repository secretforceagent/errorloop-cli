<?php

use ErrorLoop\Cli\ErrorLoopApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

it('fetches issues', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['data' => [['id' => 1, 'title' => 'Oops']]])),
    ]);

    $client = new Client(['handler' => HandlerStack::create($mock)]);
    $api = new ErrorLoopApi('https://errorloop.test', 'token');

    $reflection = new ReflectionClass($api);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($api, $client);

    $issues = $api->getIssues();

    expect($issues['data'])->toHaveCount(1);
});
