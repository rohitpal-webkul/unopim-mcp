<?php

use Webkul\Attribute\Models\Attribute;
use Webkul\Category\Models\Category;
use Webkul\MCP\Servers\UnoPimAgentServer;
use Webkul\MCP\Tools\Catalog\AttributeSearchTool;
use Webkul\MCP\Tools\Catalog\CatalogSchemaTool;
use Webkul\MCP\Tools\Catalog\CategorySearchTool;
use Webkul\MCP\Tools\Catalog\ProductSearchTool;
use Webkul\Product\Models\Product;

it('fetches catalog schema through the MCP tool', function () {
    UnoPimAgentServer::tool(CatalogSchemaTool::class)
        ->assertOk()
        ->assertSee('filterable_fields')
        ->assertSee('operators');
});

it('searches attributes through the MCP tool', function () {
    Attribute::factory()->count(2)->create();

    UnoPimAgentServer::tool(AttributeSearchTool::class, ['limit' => 2])
        ->assertOk()
        ->assertSee('attributes')
        ->assertSee('code');
});

it('searches categories through the MCP tool', function () {
    $category = Category::factory()->create();

    UnoPimAgentServer::tool(CategorySearchTool::class, ['limit' => 5])
        ->assertOk()
        ->assertSee('categories')
        ->assertSee($category->code);
});

it('searches products through the MCP tool', function () {
    $product = Product::factory()->create(['sku' => 'TEST-MCP-'.uniqid()]);

    UnoPimAgentServer::tool(ProductSearchTool::class, ['limit' => 10])
        ->assertOk()
        ->assertSee('products')
        ->assertSee($product->sku);
});
