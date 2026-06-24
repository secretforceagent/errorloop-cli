<?php

namespace ErrorLoop\Cli;

use RuntimeException;

class Config
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? $this->defaultPath();
    }

    public function getEndpoint(): string
    {
        return $this->get('endpoint', 'https://errorloop.example.com');
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->set('endpoint', rtrim($endpoint, '/'));
    }

    public function getAgentToken(): string
    {
        return $this->get('agent_token', '');
    }

    public function setAgentToken(string $token): void
    {
        $this->set('agent_token', $token);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->load();
    }

    /**
     * @return array<string, mixed>
     */
    private function load(): array
    {
        if (! file_exists($this->path)) {
            return [];
        }

        $contents = file_get_contents($this->path);

        if ($contents === false) {
            throw new RuntimeException("Could not read config file: {$this->path}");
        }

        return json_decode($contents, true) ?? [];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function save(array $config): void
    {
        $dir = dirname($this->path);

        if (! is_dir($dir) && ! mkdir($dir, 0700, true) && ! is_dir($dir)) {
            throw new RuntimeException("Could not create config directory: {$dir}");
        }

        $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new RuntimeException('Could not encode config to JSON');
        }

        if (file_put_contents($this->path, $json) === false) {
            throw new RuntimeException("Could not write config file: {$this->path}");
        }

        chmod($this->path, 0600);
    }

    private function get(string $key, string $default): string
    {
        $config = $this->load();

        return is_string($config[$key] ?? null) ? $config[$key] : $default;
    }

    private function set(string $key, string $value): void
    {
        $config = $this->load();
        $config[$key] = $value;
        $this->save($config);
    }

    private function defaultPath(): string
    {
        $home = getenv('HOME') ?: getenv('USERPROFILE');

        if (! $home) {
            throw new RuntimeException('Could not determine home directory');
        }

        return $home.'/.config/errorloop/config.json';
    }
}
