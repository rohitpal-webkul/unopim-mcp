<?php

namespace Webkul\MCP\DevTools;

use Illuminate\Support\Facades\File;
use Webkul\MCP\Contracts\FileManagerInterface;

class FileManager implements FileManagerInterface
{
    /**
     * Create a new file with the given content.
     */
    public function create(string $path, string $content): bool
    {
        $realPath = $this->validateAndResolvePath($path);

        if (File::exists($realPath)) {
            throw new \RuntimeException("File already exists at: {$path}");
        }

        File::ensureDirectoryExists(dirname($realPath));

        return File::put($realPath, $content) !== false;
    }

    /**
     * Read the content of a file.
     */
    public function read(string $path): string
    {
        $realPath = $this->validateAndResolvePath($path);

        if (! File::exists($realPath)) {
            throw new \RuntimeException("File not found at: {$path}");
        }

        return File::get($realPath);
    }

    /**
     * Update an existing file with the given content.
     */
    public function update(string $path, string $content): bool
    {
        $realPath = $this->validateAndResolvePath($path);

        if (! File::exists($realPath)) {
            throw new \RuntimeException("File does not exist at: {$path}");
        }

        return File::put($realPath, $content) !== false;
    }

    /**
     * Validate the path to ensure it is within allowed project boundaries.
     */
    private function validateAndResolvePath(string $path): string
    {
        $allowedPaths = config('mcp.allowed_paths', [base_path(), sys_get_temp_dir()]);

        // Resolve to absolute path
        $absolutePath = $this->isAbsolutePath($path)
            ? $path
            : base_path().DIRECTORY_SEPARATOR.$path;

        // Normalise without requiring existence
        $normalised = $this->normalisePath($absolutePath);

        $isAllowed = false;
        foreach ($allowedPaths as $allowed) {
            $realAllowed = realpath($allowed);
            if ($realAllowed && str_starts_with($normalised, $realAllowed)) {
                $isAllowed = true;
                break;
            }
        }

        if (! $isAllowed) {
            throw new \RuntimeException("Security: Access to path [{$path}] is restricted. It must be within allowed project boundaries.");
        }

        return $normalised;
    }

    /**
     * Normalise a path (resolve .. / .) without requiring the path to exist.
     */
    private function normalisePath(string $path): string
    {
        // Replace all slashes with the OS separator
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $resolved = [];

        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($resolved);
            } else {
                $resolved[] = $part;
            }
        }

        $result = implode(DIRECTORY_SEPARATOR, $resolved);

        // Re-add leading separator for Linux/Unix paths if the original path was absolute
        if (! $this->isWindowsPath($path) && str_starts_with($path, DIRECTORY_SEPARATOR)) {
            $result = DIRECTORY_SEPARATOR.$result;
        }

        return $result;
    }

    /**
     * Check if a path is on Windows and absolute (e.g., C:\).
     */
    private function isWindowsPath(string $path): bool
    {
        return strlen($path) > 1 && $path[1] === ':';
    }

    /**
     * Check if a path is absolute.
     */
    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || (strlen($path) > 2 && $path[1] === ':' && ($path[2] === '\\' || $path[2] === '/'));
    }
}
