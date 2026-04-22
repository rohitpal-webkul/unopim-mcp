<?php

namespace Webkul\MCP\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SkillLoader
{
    /**
     * In-memory list of loaded skills (array of parsed skill arrays).
     *
     * @var array<string, array<string,mixed>>
     */
    private array $skills = [];

    /**
     * Whether skills have been loaded in this request lifecycle.
     */
    private bool $loaded = false;

    public function __construct(protected SkillParser $parser) {}

    /**
     * Return all loaded skills, keyed by their normalized tool name.
     *
     * @return array<string, array<string,mixed>>
     */
    public function all(): array
    {
        if (! $this->loaded) {
            $this->load();
        }

        return $this->skills;
    }

    /**
     * Find a skill by its name or normalized tool name.
     *
     * @return array<string,mixed>|null
     */
    public function find(string $name): ?array
    {
        $skills = $this->all();

        // Exact tool-key match first.
        if (isset($skills[$name])) {
            return $skills[$name];
        }

        // Case-insensitive name match.
        foreach ($skills as $skill) {
            if (Str::lower($skill['name']) === Str::lower($name)) {
                return $skill;
            }
        }

        return null;
    }

    /**
     * Force reload skills from the filesystem, bypassing cache.
     */
    public function reload(): void
    {
        Cache::forget(config('mcp.cache_key', 'mcp.skills'));

        $this->loaded = false;
        $this->skills = [];

        $this->load();
    }

    /**
     * Load skills from cache or filesystem.
     */
    private function load(): void
    {
        $useCache = (bool) config('mcp.enable_cache', true);
        $cacheKey = config('mcp.cache_key', 'mcp.skills');
        $cacheTtl = (int) config('mcp.cache_ttl', 3600);

        if ($useCache) {
            $this->skills = Cache::remember($cacheKey, $cacheTtl, fn () => $this->scanSkills());
        } else {
            $this->skills = $this->scanSkills();
        }

        $this->loaded = true;
    }

    /**
     * Scan the skills path and parse each SKILL.md found.
     *
     * @return array<string, array<string,mixed>>
     */
    private function scanSkills(): array
    {
        $skillsPath = config('mcp.skills_path', base_path('.ai/skills'));

        if (! is_dir($skillsPath)) {
            return [];
        }

        $results = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($skillsPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getFilename() !== 'SKILL.md') {
                continue;
            }

            $realPath = $file->getRealPath();

            // Safety check — ensure the file is actually inside the skills path.
            if (! $this->isPathSafe($realPath, $skillsPath)) {
                continue;
            }

            try {
                $skill = $this->parser->parse($realPath);

                // Skip skills with no name.
                if (empty($skill['name'])) {
                    continue;
                }

                $toolKey = $this->normalizeToolName($skill['name']);

                if (isset($results[$toolKey])) {
                    $existingPath = $results[$toolKey]['path'] ?? 'unknown';
                    Log::warning("MCP Skill Conflict: Tool name [{$toolKey}] already exists from [{$existingPath}]. Skipping duplicates found in [{$realPath}].");

                    continue;
                }

                $results[$toolKey] = array_merge($skill, ['tool_key' => $toolKey]);
            } catch (\Throwable) {
                // Skip unparseable SKILL.md files silently.
                continue;
            }
        }

        return $results;
    }

    /**
     * Normalize a skill name into a safe snake_case tool key.
     */
    public function normalizeToolName(string $name): string
    {
        return Str::snake(preg_replace('/[^a-zA-Z0-9_\- ]/', '', $name));
    }

    /**
     * Verify that a resolved path is safely inside the allowed base directory.
     */
    private function isPathSafe(string $realPath, string $baseDir): bool
    {
        $realBase = realpath($baseDir);

        if ($realBase === false) {
            return false;
        }

        return str_starts_with($realPath, $realBase.DIRECTORY_SEPARATOR);
    }
}
