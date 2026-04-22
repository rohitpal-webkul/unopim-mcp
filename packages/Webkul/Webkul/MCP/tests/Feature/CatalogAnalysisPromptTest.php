<?php

use Laravel\Mcp\Request;
use Webkul\MCP\Prompts\Catalog\CatalogAnalysisPrompt;

it('exposes a single optional focus argument', function () {
    $prompt = app(CatalogAnalysisPrompt::class);
    $arguments = $prompt->arguments();

    expect($arguments)->toHaveCount(1);
    expect($arguments[0]->name)->toBe('focus');
    expect($arguments[0]->required)->toBeFalse();
});

it('defaults focus to completeness when not supplied', function () {
    $prompt = app(CatalogAnalysisPrompt::class);

    $request = new Request([]);
    $response = $prompt->handle($request);
    $text = (string) $response->content();

    expect($text)->toContain('Completeness');
});

it('echoes the requested focus into the plan', function () {
    $prompt = app(CatalogAnalysisPrompt::class);

    $request = new Request(['focus' => 'optimization']);
    $response = $prompt->handle($request);
    $text = (string) $response->content();

    expect($text)->toContain('Optimization');
});

it('tells the caller to read the catalog schema resource first', function () {
    $prompt = app(CatalogAnalysisPrompt::class);
    $text = (string) $prompt->handle(new Request([]))->content();

    expect($text)->toContain('catalog-schema')
        ->and($text)->toContain('search_attributes')
        ->and($text)->toContain('search_products');
});
