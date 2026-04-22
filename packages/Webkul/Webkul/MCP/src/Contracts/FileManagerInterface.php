<?php

namespace Webkul\MCP\Contracts;

interface FileManagerInterface
{
    /**
     * Create a new file with the given content.
     */
    public function create(string $path, string $content): bool;

    /**
     * Read the content of a file.
     */
    public function read(string $path): string;

    /**
     * Update an existing file with the given content.
     */
    public function update(string $path, string $content): bool;
}
