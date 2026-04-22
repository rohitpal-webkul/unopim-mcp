<?php

use Illuminate\Support\Facades\RateLimiter;
use Webkul\MCP\Servers\UnoPimAgentServer;
use Webkul\MCP\Tools\Catalog\ProductSearchTool;

it('enforces rate limiting', function () {
    $toolName = 'search_products';

    RateLimiter::shouldReceive('tooManyAttempts')
        ->withArgs(fn ($key, $max) => str_contains($key, "mcp-tool:{$toolName}") && $max === 60)
        ->once()
        ->andReturn(true);

    UnoPimAgentServer::tool(ProductSearchTool::class)
        ->assertHasErrors(['Rate limit exceeded']);
});

it('allows execution when not rate limited', function () {
    RateLimiter::shouldReceive('tooManyAttempts')->andReturn(false);
    RateLimiter::shouldReceive('hit')->atLeast()->once();

    UnoPimAgentServer::tool(ProductSearchTool::class)->assertOk();
});

it('skips authorization for guest/cli context', function () {
    UnoPimAgentServer::tool(ProductSearchTool::class)->assertOk();
});
