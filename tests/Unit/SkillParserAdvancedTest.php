<?php

use Webkul\MCP\Services\SkillParser;

beforeEach(function () {
    $this->parser = new SkillParser;
});

it('throws exception for nonexistent file', function () {
    expect(fn () => $this->parser->parse('/nonexistent/path/SKILL.md'))
        ->toThrow(InvalidArgumentException::class, 'SKILL.md not found');
});

it('parses parameters from frontmatter', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
---
name: parameterized-skill
description: Has parameters
parameters:
  query:
    type: string
    required: true
  limit:
    type: integer
    default: 10
---

# Parameterized Skill
Uses parameters.
MD;

    file_put_contents($tempFile, $content);
    $result = $this->parser->parse($tempFile);

    expect($result['parameters'])->toBeArray();
    expect($result['parameters'])->toHaveKey('query');
    expect($result['parameters']['query']['type'])->toBe('string');
    expect($result['parameters']['query']['required'])->toBeTrue();
    expect($result['parameters']['limit']['default'])->toBe(10);

    unlink($tempFile);
});

it('parses metadata from frontmatter', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
---
name: metadata-skill
description: Has metadata
metadata:
  author: john
  version: 2.0
  tags:
    - catalog
    - automation
---

Body text.
MD;

    file_put_contents($tempFile, $content);
    $result = $this->parser->parse($tempFile);

    expect($result['metadata']['author'])->toBe('john');
    expect($result['metadata']['version'])->toBe(2.0);
    expect($result['metadata']['tags'])->toContain('catalog');
    expect($result['metadata']['tags'])->toContain('automation');

    unlink($tempFile);
});

it('parses license field', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
---
name: licensed-skill
description: With license
license: Apache-2.0
---

Content.
MD;

    file_put_contents($tempFile, $content);
    $result = $this->parser->parse($tempFile);

    expect($result['license'])->toBe('Apache-2.0');

    unlink($tempFile);
});

it('handles frontmatter with only name', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
---
name: minimal-skill
---

Minimal body.
MD;

    file_put_contents($tempFile, $content);
    $result = $this->parser->parse($tempFile);

    expect($result['name'])->toBe('minimal-skill');
    expect($result['description'])->toBe('');
    expect($result['license'])->toBe('');
    expect($result['metadata'])->toBeEmpty();
    expect($result['content'])->toBe('Minimal body.');

    unlink($tempFile);
});

it('handles empty file', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');
    file_put_contents($tempFile, '');

    $result = $this->parser->parse($tempFile);

    expect($result['name'])->toBe('');
    expect($result['content'])->toBe('');

    unlink($tempFile);
});

it('handles frontmatter with no body', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
---
name: header-only
description: No body content
---
MD;

    file_put_contents($tempFile, $content);
    $result = $this->parser->parse($tempFile);

    expect($result['name'])->toBe('header-only');
    expect($result['content'])->toBe('');

    unlink($tempFile);
});

it('preserves multiline body content', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
---
name: multiline-skill
description: Has multiline body
---

# Heading

Paragraph one.

Paragraph two.

- List item 1
- List item 2
MD;

    file_put_contents($tempFile, $content);
    $result = $this->parser->parse($tempFile);

    expect($result['content'])->toContain('# Heading');
    expect($result['content'])->toContain('Paragraph one.');
    expect($result['content'])->toContain('Paragraph two.');
    expect($result['content'])->toContain('- List item 1');

    unlink($tempFile);
});

it('includes file path in result', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');
    file_put_contents($tempFile, "---\nname: path-test\n---\nBody");

    $result = $this->parser->parse($tempFile);

    expect($result['path'])->toBe($tempFile);

    unlink($tempFile);
});

it('handles content that looks like frontmatter but is not at the start', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'skill_');

    $content = <<<'MD'
Some text before frontmatter.

---
name: not-frontmatter
---

More text.
MD;

    file_put_contents($tempFile, $content);
    $result = $this->parser->parse($tempFile);

    // Since frontmatter is not at the start, defaults should be used
    expect($result['name'])->toBe('');
    expect($result['content'])->toContain('Some text before frontmatter.');

    unlink($tempFile);
});
