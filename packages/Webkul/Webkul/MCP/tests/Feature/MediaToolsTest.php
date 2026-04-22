<?php

/**
 * Media tool tests.
 *
 * Note: Dedicated Media/DAM MCP tools (MediaUploadTool, AssetCreateTool,
 * AssetListTool, DirectoryListTool) are not yet implemented as standalone
 * tools in the ToolRegistry. Media operations can currently be performed
 * via the dev_tools action 'create_file' for file operations within
 * allowed paths.
 *
 * The mcp.media config section defines allowed extensions and MIME types
 * that will be enforced when media upload tools are added.
 */

use Illuminate\Support\Facades\Config;

it('defines allowed media extensions in config', function () {
    $extensions = Config::get('mcp.media.allowed_extensions');

    expect($extensions)->toBeArray();
    expect($extensions)->toContain('jpg');
    expect($extensions)->toContain('png');
    expect($extensions)->toContain('pdf');
    expect($extensions)->toContain('csv');
    expect($extensions)->not->toContain('exe');
    expect($extensions)->not->toContain('sh');
});

it('defines allowed MIME types in config', function () {
    $mimes = Config::get('mcp.media.allowed_mimes');

    expect($mimes)->toBeArray();
    expect($mimes)->toContain('image/jpeg');
    expect($mimes)->toContain('image/png');
    expect($mimes)->toContain('application/pdf');
    expect($mimes)->toContain('text/csv');
});
