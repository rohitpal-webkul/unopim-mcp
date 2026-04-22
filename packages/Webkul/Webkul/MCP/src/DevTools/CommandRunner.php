<?php

namespace Webkul\MCP\DevTools;

use Symfony\Component\Process\Process;

class CommandRunner
{
    /**
     * Allowed base commands.
     */
    protected array $allowedCommands = [
        'php artisan',
        'composer',
    ];

    /**
     * Parse and run a command safely without invoking a shell.
     */
    public function run(string $command): string
    {
        $tokens = $this->parseCommand($command);
        $this->ensureCommandIsAllowed($tokens);

        $process = new Process($tokens, base_path());
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput() ?: $process->getOutput());
        }

        return $process->getOutput();
    }

    /**
     * Parse the string into an array of arguments to prevent shell interpolation.
     */
    private function parseCommand(string $command): array
    {
        // Simple tokenization by space, respecting quotes.
        $tokens = str_getcsv($command, ' ', '"');
        $tokens = array_values(array_filter($tokens, fn ($t) => trim($t) !== ''));

        if (empty($tokens)) {
            throw new \RuntimeException('Command is empty.');
        }

        return $tokens;
    }

    /**
     * Ensure the command starts with an allowed base command and contains no shell syntax.
     */
    private function ensureCommandIsAllowed(array $tokens): void
    {
        $isAllowed = false;

        if ($tokens[0] === 'php' && isset($tokens[1]) && $tokens[1] === 'artisan') {
            $isAllowed = true;
        } elseif ($tokens[0] === 'composer') {
            $isAllowed = true;
        }

        if (! $isAllowed) {
            throw new \RuntimeException('Command not allowed: '.htmlspecialchars($tokens[0]));
        }

        foreach ($tokens as $token) {
            // Even though array Process execution protects against shell operators,
            // we proactively block them as an additional sanitization measure.
            if (preg_match('/[;&|`<>]/', $token) || str_contains($token, '$(')) {
                throw new \RuntimeException('Dangerous characters detected in command arguments.');
            }
        }
    }
}
