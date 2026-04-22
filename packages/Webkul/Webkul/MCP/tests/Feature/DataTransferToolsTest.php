<?php

/**
 * DataTransfer tool tests.
 *
 * Note: DataTransfer monitoring (import/export job status) is not yet
 * implemented as standalone MCP tools. When these tools are added to the
 * ToolRegistry, uncomment and update the tests below.
 *
 * The import/export job status can currently be checked via the dev_tools
 * action 'run_command' with `php artisan queue:work` or by querying
 * the job_tracks table directly via the search tools.
 */

use Webkul\MCP\Servers\UnoPimAgentServer;
use Webkul\MCP\Tools\Dev\DevToolsTool;

it('can run artisan commands for data transfer status via dev_tools', function () {
    UnoPimAgentServer::tool(DevToolsTool::class, [
        'action' => 'run_command',
        'params' => ['command' => 'php artisan --version'],
    ])->assertOk();
});
