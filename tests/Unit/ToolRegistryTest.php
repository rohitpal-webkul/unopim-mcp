<?php

use Webkul\MCP\Registry\ToolRegistry;
use Webkul\MCP\Tools\Catalog\AttributeSearchTool;
use Webkul\MCP\Tools\Catalog\AttributeUpsertTool;
use Webkul\MCP\Tools\Catalog\CatalogSchemaTool;
use Webkul\MCP\Tools\Catalog\CategorySearchTool;
use Webkul\MCP\Tools\Catalog\CategoryUpsertTool;
use Webkul\MCP\Tools\Catalog\ProductGetTool;
use Webkul\MCP\Tools\Catalog\ProductSearchTool;
use Webkul\MCP\Tools\Catalog\ProductUpsertTool;
use Webkul\MCP\Tools\Dev\DevToolsTool;
use Webkul\MCP\Tools\Dev\RunSkillTool;
use Webkul\MCP\Tools\Settings\SettingSearchTool;
use Webkul\MCP\Tools\Settings\SettingUpsertTool;

it('returns exactly 12 registered tools', function () {
    $tools = ToolRegistry::tools();

    expect($tools)->toHaveCount(12);
});

it('contains all catalog tools', function () {
    $tools = ToolRegistry::tools();

    expect($tools)->toContain(CatalogSchemaTool::class);
    expect($tools)->toContain(ProductSearchTool::class);
    expect($tools)->toContain(ProductGetTool::class);
    expect($tools)->toContain(ProductUpsertTool::class);
    expect($tools)->toContain(CategorySearchTool::class);
    expect($tools)->toContain(CategoryUpsertTool::class);
    expect($tools)->toContain(AttributeSearchTool::class);
    expect($tools)->toContain(AttributeUpsertTool::class);
});

it('contains all settings tools', function () {
    $tools = ToolRegistry::tools();

    expect($tools)->toContain(SettingSearchTool::class);
    expect($tools)->toContain(SettingUpsertTool::class);
});

it('contains all dev tools', function () {
    $tools = ToolRegistry::tools();

    expect($tools)->toContain(DevToolsTool::class);
    expect($tools)->toContain(RunSkillTool::class);
});

it('returns an array of class strings', function () {
    $tools = ToolRegistry::tools();

    foreach ($tools as $tool) {
        expect($tool)->toBeString();
        expect(class_exists($tool))->toBeTrue();
    }
});

it('contains CatalogSchemaTool as the first entry', function () {
    $tools = ToolRegistry::tools();

    expect($tools[0])->toBe(CatalogSchemaTool::class);
});
