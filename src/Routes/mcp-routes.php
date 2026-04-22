<?php

use Laravel\Mcp\Facades\Mcp;
use Webkul\MCP\Servers\UnoPimAgentServer;

Mcp::web('mcp/unopim', UnoPimAgentServer::class);
