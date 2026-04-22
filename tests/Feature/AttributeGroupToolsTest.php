<?php

use Webkul\Attribute\Models\AttributeGroup;
use Webkul\MCP\Servers\UnoPimAgentServer;
use Webkul\MCP\Tools\Catalog\AttributeGroupSearchTool;
use Webkul\MCP\Tools\Catalog\AttributeGroupUpsertTool;

it('searches attribute groups through the mcp tool', function () {
    UnoPimAgentServer::tool(AttributeGroupSearchTool::class)
        ->assertOk()
        ->assertSee('groups');
});

it('creates and updates attribute groups via upsert', function () {
    $code = 'tag_'.rand(100, 999);

    // Create via upsert
    UnoPimAgentServer::tool(AttributeGroupUpsertTool::class, [
        'items' => [
            [
                'code' => $code,
                'name' => 'Test Group',
            ],
        ],
    ])->assertOk()->assertSee($code)->assertSee('created');

    $group = AttributeGroup::where('code', $code)->first();
    expect($group)->not->toBeNull();

    // Update via upsert
    UnoPimAgentServer::tool(AttributeGroupUpsertTool::class, [
        'items' => [
            [
                'code' => $code,
                'name' => 'Updated Test Group',
            ],
        ],
    ])->assertOk()->assertSee('updated');

    // Cleanup
    if ($group) {
        $group->delete();
    }
});
