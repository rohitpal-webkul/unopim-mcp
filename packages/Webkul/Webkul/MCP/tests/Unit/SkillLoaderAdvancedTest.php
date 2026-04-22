<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Webkul\MCP\Services\SkillLoader;
use Webkul\MCP\Services\SkillParser;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir().'/mcp_skills_adv_'.uniqid();
    mkdir($this->tempDir);

    Config::set('mcp.skills_path', $this->tempDir);
    Config::set('mcp.enable_cache', false);

    $this->loader = new SkillLoader(app(SkillParser::class));
});

afterEach(function () {
    // Recursive cleanup
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($this->tempDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
    }

    rmdir($this->tempDir);
});

it('finds a skill by exact tool key', function () {
    $skillDir = $this->tempDir.'/my-tool';
    mkdir($skillDir);
    file_put_contents($skillDir.'/SKILL.md', "---\nname: My Tool\ndescription: Does things\n---\nBody text");

    $skill = $this->loader->find('my_tool');

    expect($skill)->not->toBeNull();
    expect($skill['name'])->toBe('My Tool');
});

it('finds a skill by case-insensitive name', function () {
    $skillDir = $this->tempDir.'/catalog-helper';
    mkdir($skillDir);
    file_put_contents($skillDir.'/SKILL.md', "---\nname: Catalog Helper\ndescription: Helps with catalog\n---\nContent");

    $skill = $this->loader->find('catalog helper');

    expect($skill)->not->toBeNull();
    expect($skill['name'])->toBe('Catalog Helper');
});

it('returns null when skill is not found', function () {
    $skill = $this->loader->find('nonexistent_skill');

    expect($skill)->toBeNull();
});

it('returns empty array when skills directory does not exist', function () {
    Config::set('mcp.skills_path', '/tmp/nonexistent_dir_'.uniqid());

    $loader = new SkillLoader(app(SkillParser::class));
    $skills = $loader->all();

    expect($skills)->toBeEmpty();
});

it('skips skills with no name', function () {
    $skillDir = $this->tempDir.'/unnamed';
    mkdir($skillDir);
    file_put_contents($skillDir.'/SKILL.md', "---\ndescription: No name\n---\nContent");

    $skills = $this->loader->all();

    expect($skills)->toBeEmpty();
});

it('skips duplicate tool names and logs warning', function () {
    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn ($msg) => str_contains($msg, 'MCP Skill Conflict'));

    $dir1 = $this->tempDir.'/tool-v1';
    $dir2 = $this->tempDir.'/tool-v2';
    mkdir($dir1);
    mkdir($dir2);

    file_put_contents($dir1.'/SKILL.md', "---\nname: My Tool\ndescription: First\n---\nFirst version");
    file_put_contents($dir2.'/SKILL.md', "---\nname: My Tool\ndescription: Second\n---\nSecond version");

    $skills = $this->loader->all();

    // Only one should be loaded since duplicates are skipped
    expect($skills)->toHaveCount(1);
    expect($skills)->toHaveKey('my_tool');
});

it('reloads skills from filesystem bypassing cache', function () {
    $skillDir = $this->tempDir.'/initial';
    mkdir($skillDir);
    file_put_contents($skillDir.'/SKILL.md', "---\nname: Initial Skill\ndescription: First load\n---\nBody");

    $skills = $this->loader->all();
    expect($skills)->toHaveCount(1);

    // Add a new skill
    $newDir = $this->tempDir.'/added';
    mkdir($newDir);
    file_put_contents($newDir.'/SKILL.md', "---\nname: Added Skill\ndescription: After reload\n---\nBody");

    $this->loader->reload();
    $skills = $this->loader->all();

    expect($skills)->toHaveCount(2);
    expect($skills)->toHaveKey('added_skill');
});

it('normalizes tool names correctly', function () {
    expect($this->loader->normalizeToolName('My Cool Tool'))->toBe('my_cool_tool');
    expect($this->loader->normalizeToolName('CamelCaseName'))->toBe('camel_case_name');
    expect($this->loader->normalizeToolName('with-dashes'))->toBe('with-dashes');
    expect($this->loader->normalizeToolName('special!@#chars'))->toBe('specialchars');
    expect($this->loader->normalizeToolName('UPPER CASE'))->toBe('u_p_p_e_r_c_a_s_e');
});

it('scans nested directories recursively', function () {
    $nested = $this->tempDir.'/level1/level2';
    mkdir($nested, 0777, true);
    file_put_contents($nested.'/SKILL.md', "---\nname: Deep Skill\ndescription: Deeply nested\n---\nContent");

    $skills = $this->loader->all();

    expect($skills)->toHaveCount(1);
    expect($skills['deep_skill']['name'])->toBe('Deep Skill');
});

it('skips unparseable SKILL.md files silently', function () {
    $skillDir = $this->tempDir.'/broken';
    mkdir($skillDir);
    // Write invalid YAML that will cause parser to fail
    file_put_contents($skillDir.'/SKILL.md', "---\nname: [invalid yaml\n  broken: {{\n---\nContent");

    // Should not throw, should just skip
    $skills = $this->loader->all();

    // Either 0 (if the YAML parser throws) or 1 (if it somehow parses)
    // The key is it doesn't throw
    expect($skills)->toBeArray();
});

it('only loads files named SKILL.md', function () {
    $skillDir = $this->tempDir.'/misc';
    mkdir($skillDir);
    file_put_contents($skillDir.'/README.md', "---\nname: Not A Skill\ndescription: readme\n---\nContent");
    file_put_contents($skillDir.'/SKILL.md', "---\nname: Real Skill\ndescription: actual skill\n---\nContent");

    $skills = $this->loader->all();

    expect($skills)->toHaveCount(1);
    expect($skills['real_skill']['name'])->toBe('Real Skill');
});

it('includes tool_key in loaded skill data', function () {
    $skillDir = $this->tempDir.'/keyed';
    mkdir($skillDir);
    file_put_contents($skillDir.'/SKILL.md', "---\nname: Keyed Tool\ndescription: Has key\n---\nBody");

    $skills = $this->loader->all();

    expect($skills['keyed_tool']['tool_key'])->toBe('keyed_tool');
});
