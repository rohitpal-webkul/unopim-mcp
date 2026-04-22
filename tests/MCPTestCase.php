<?php

namespace Webkul\MCP\Tests;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MCPTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Disable MCP API auth by default for all MCP tests.
        // Feature tests like SecurityTest will explicitly re-enable it if needed.
        Config::set('mcp.api_auth', false);
    }
}
