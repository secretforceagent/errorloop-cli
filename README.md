# ErrorLoop CLI

[![Latest Version](https://img.shields.io/github/v/tag/secretforceagent/errorloop-cli)](https://github.com/secretforceagent/errorloop-cli/tags)

A standalone command-line client for managing issues on an [ErrorLoop](https://github.com/secretforceagent/errorloop) server.

## Requirements

- PHP 8.3+
- Composer

## Installation

### Global install (recommended)

```bash
composer global require errorloop/cli
```

Make sure `~/.composer/vendor/bin` or `~/.config/composer/vendor/bin` is in your `PATH`.

### Project-local install

```bash
composer require errorloop/cli
./vendor/bin/errorloop issues --status open
```

## Configuration

Run the config command once:

```bash
errorloop config --endpoint https://er.ma.rs --token your-agent-token
```

This writes `~/.config/errorloop/config.json` with `0600` permissions.

You can also use environment variables, which take precedence over the config file:

```bash
export ERRORLOOP_ENDPOINT=https://er.ma.rs
export ERRORLOOP_AGENT_TOKEN=your-agent-token
```

The agent token is configured on the ErrorLoop server, not per-project.

## Usage

Create a project (returns the API key for the Laravel SDK):

```bash
errorloop create-project babyprocare
```

List projects:

```bash
errorloop projects
```

List open issues:

```bash
errorloop issues --status open
```

Show a single issue:

```bash
errorloop issue 123 --for-agent
```

Claim an issue to work on it:

```bash
errorloop claim 123
```

Record a fix attempt:

```bash
errorloop fix-attempted 123 --commit abc123 --agent $(whoami)
```

Record a deploy:

```bash
errorloop deploy --project babyprocare --sha abc123
```

Verify an issue is resolved:

```bash
errorloop verify 123
```

## License

MIT
