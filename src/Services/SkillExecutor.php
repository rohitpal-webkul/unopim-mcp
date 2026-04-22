<?php

namespace Webkul\MCP\Services;

use Webkul\MCP\Contracts\FileManagerInterface;
use Webkul\MCP\Contracts\SkillExecutorInterface;
use Webkul\MCP\DevTools\CommandRunner;
use Webkul\MCP\DevTools\PluginGenerator;
use Webkul\MCP\DevTools\TestGenerator;

class SkillExecutor implements SkillExecutorInterface
{
    public function __construct(
        protected FileManagerInterface $fileManager,
        protected CommandRunner $commandRunner,
        protected PluginGenerator $pluginGenerator,
        protected TestGenerator $testGenerator
    ) {}

    public function createFile(string $path, string $content): bool
    {
        return $this->fileManager->create($path, $content);
    }

    public function readFile(string $path): string
    {
        return $this->fileManager->read($path);
    }

    public function updateFile(string $path, string $content): bool
    {
        return $this->fileManager->update($path, $content);
    }

    public function runCommand(string $command): string
    {
        return $this->commandRunner->run($command);
    }

    public function generatePlugin(string $name, string $type = 'connector'): array
    {
        return $this->pluginGenerator->generate($name, $type);
    }

    public function generateTest(string $packageName, string $className): string
    {
        return $this->testGenerator->generate($packageName, $className);
    }

    public function executeSkill(string $skillName, array $args = []): string
    {
        // This is a placeholder for dynamic skill execution logic if needed.
        // Currently skills are loaded via SkillLoader and registered as tools.
        return "Skill [{$skillName}] execution triggered with ".count($args).' arguments.';
    }
}
