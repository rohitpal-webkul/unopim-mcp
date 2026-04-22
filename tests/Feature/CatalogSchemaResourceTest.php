<?php

use Webkul\MCP\Resources\Catalog\CatalogSchemaResource;

it('renders the catalog schema resource with counts', function () {
    $resource = app(CatalogSchemaResource::class);
    $response = $resource->handle();

    // Laravel MCP wraps text responses in a content object. Inspect the raw text.
    $text = (string) $response->content();

    expect($text)->toContain('UnoPim Catalog Summary')
        ->and($text)->toContain('Total Products')
        ->and($text)->toContain('Total Categories')
        ->and($text)->toContain('Total Attributes');
});

it('points the user at discovery tools in the next steps section', function () {
    $resource = app(CatalogSchemaResource::class);
    $text = (string) $resource->handle()->content();

    expect($text)->toContain('search_attributes')
        ->and($text)->toContain('search_categories')
        ->and($text)->toContain('get_catalog_schema');
});
