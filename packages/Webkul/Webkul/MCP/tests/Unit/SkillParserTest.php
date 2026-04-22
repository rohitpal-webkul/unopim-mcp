<?php

use Webkul\MCP\Services\SkillParser;

it('parses valid yaml frontmatter from a skill file', function () {
    $parser = new SkillParser;
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
---
name: test-skill
description: A dummy test skill
license: MIT
metadata:
  author: tester
---

# Title
This is the body.
MD;

    file_put_contents($tempFile, $content);

    $result = $parser->parse($tempFile);

    expect($result['name'])->toBe('test-skill');
    expect($result['description'])->toBe('A dummy test skill');
    expect($result['license'])->toBe('MIT');
    expect($result['metadata']['author'])->toBe('tester');
    expect($result['content'])->toBe("# Title\nThis is the body.");
    expect($result['path'])->toBe($tempFile);

    unlink($tempFile);
});

it('falls back to defaults if no frontmatter is found', function () {
    $parser = new SkillParser;
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = 'Just some markdown text without frontmatter.';
    file_put_contents($tempFile, $content);

    $result = $parser->parse($tempFile);

    expect($result['name'])->toBe('');
    expect($result['content'])->toBe('Just some markdown text without frontmatter.');

    unlink($tempFile);
});
