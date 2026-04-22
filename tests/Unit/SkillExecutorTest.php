<?php

use Webkul\MCP\Contracts\FileManagerInterface;
use Webkul\MCP\DevTools\CommandRunner;
use Webkul\MCP\DevTools\PluginGenerator;
use Webkul\MCP\DevTools\TestGenerator;
use Webkul\MCP\Services\SkillExecutor;

beforeEach(function () {
    $this->fileManager = Mockery::mock(FileManagerInterface::class);
    $this->commandRunner = Mockery::mock(CommandRunner::class);
    $this->pluginGenerator = Mockery::mock(PluginGenerator::class);
    $this->testGenerator = Mockery::mock(TestGenerator::class);

    $this->executor = new SkillExecutor(
        $this->fileManager,
        $this->commandRunner,
        $this->pluginGenerator,
        $this->testGenerator
    );
});

afterEach(function () {
    Mockery::close();
});

it('delegates createFile to FileManager', function () {
    $this->fileManager->shouldReceive('create')
        ->once()
        ->with('path/to/file.txt', 'content here')
        ->andReturn(true);

    expect($this->executor->createFile('path/to/file.txt', 'content here'))->toBeTrue();
});

it('delegates readFile to FileManager', function () {
    $this->fileManager->shouldReceive('read')
        ->once()
        ->with('path/to/file.txt')
        ->andReturn('file contents');

    expect($this->executor->readFile('path/to/file.txt'))->toBe('file contents');
});

it('delegates updateFile to FileManager', function () {
    $this->fileManager->shouldReceive('update')
        ->once()
        ->with('path/to/file.txt', 'updated content')
        ->andReturn(true);

    expect($this->executor->updateFile('path/to/file.txt', 'updated content'))->toBeTrue();
});

it('delegates runCommand to CommandRunner', function () {
    $this->commandRunner->shouldReceive('run')
        ->once()
        ->with('php artisan migrate')
        ->andReturn('Migration successful');

    expect($this->executor->runCommand('php artisan migrate'))->toBe('Migration successful');
});

it('delegates generatePlugin to PluginGenerator with default type', function () {
    $this->pluginGenerator->shouldReceive('generate')
        ->once()
        ->with('MyPlugin', 'connector')
        ->andReturn(['name' => 'MyPlugin', 'message' => 'generated']);

    $result = $this->executor->generatePlugin('MyPlugin');

    expect($result['name'])->toBe('MyPlugin');
});

it('delegates generatePlugin to PluginGenerator with custom type', function () {
    $this->pluginGenerator->shouldReceive('generate')
        ->once()
        ->with('MyPlugin', 'generic')
        ->andReturn(['name' => 'MyPlugin', 'message' => 'generated']);

    $result = $this->executor->generatePlugin('MyPlugin', 'generic');

    expect($result['name'])->toBe('MyPlugin');
});

it('delegates generateTest to TestGenerator', function () {
    $this->testGenerator->shouldReceive('generate')
        ->once()
        ->with('Webkul/MCP', 'Services/SkillLoader')
        ->andReturn('packages/Webkul/MCP/tests/Unit/Services/SkillLoaderTest.php');

    $result = $this->executor->generateTest('Webkul/MCP', 'Services/SkillLoader');

    expect($result)->toContain('SkillLoaderTest.php');
});

it('returns placeholder message for executeSkill', function () {
    $result = $this->executor->executeSkill('my-skill', ['arg1' => 'val1', 'arg2' => 'val2']);

    expect($result)->toContain('my-skill');
    expect($result)->toContain('2 arguments');
});

it('returns placeholder message for executeSkill with no args', function () {
    $result = $this->executor->executeSkill('empty-skill');

    expect($result)->toContain('empty-skill');
    expect($result)->toContain('0 arguments');
});

it('propagates FileManager exceptions', function () {
    $this->fileManager->shouldReceive('read')
        ->once()
        ->andThrow(new RuntimeException('Security: Access restricted'));

    expect(fn () => $this->executor->readFile('../../etc/passwd'))
        ->toThrow(RuntimeException::class, 'Security');
});

it('propagates CommandRunner exceptions', function () {
    $this->commandRunner->shouldReceive('run')
        ->once()
        ->andThrow(new RuntimeException('Command not allowed'));

    expect(fn () => $this->executor->runCommand('rm -rf /'))
        ->toThrow(RuntimeException::class, 'Command not allowed');
});
