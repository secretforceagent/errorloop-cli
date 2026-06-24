<?php

namespace ErrorLoop\Cli;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ErrorLoopApi
{
    private Client $client;

    public function __construct(
        private string $endpoint,
        private string $agentToken,
    ) {
        $this->client = new Client([
            'base_uri' => rtrim($endpoint, '/').'/api/',
            'timeout' => 5,
            'connect_timeout' => 2,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$agentToken,
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function getIssues(?string $status = null, ?int $projectId = null): array
    {
        $query = array_filter([
            'status' => $status,
            'project_id' => $projectId,
        ], fn ($value) => $value !== null);

        return $this->get('issues', $query);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function getIssue(int $id): array
    {
        return $this->get("issues/{$id}");
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function claimIssue(int $id): array
    {
        return $this->post("issues/{$id}/claim", []);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function recordFixAttempt(int $id, array $data): array
    {
        return $this->post("issues/{$id}/fix-attempts", $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function recordDeploy(array $data): array
    {
        return $this->post('deploys', $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function createProject(array $data): array
    {
        return $this->post('projects', $data);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function getIssueExamples(int $id): array
    {
        return $this->get("issues/{$id}/examples");
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    public function getProjects(): array
    {
        return $this->get('projects');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    private function get(string $path, array $query = []): array
    {
        $response = $this->client->get($path, ['query' => $query]);

        return json_decode($response->getBody()->getContents(), true) ?? [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws GuzzleException
     */
    private function post(string $path, array $data): array
    {
        $response = $this->client->post($path, ['json' => $data]);

        return json_decode($response->getBody()->getContents(), true) ?? [];
    }
}
