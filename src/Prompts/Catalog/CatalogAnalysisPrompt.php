<?php

namespace Webkul\MCP\Prompts\Catalog;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class CatalogAnalysisPrompt extends Prompt
{
    /**
     * The prompt's name.
     */
    protected string $name = 'analyze-catalog';

    /**
     * The prompt's title.
     */
    protected string $title = 'Analyze UnoPim Catalog';

    /**
     * The prompt's description.
     */
    protected string $description = 'Get a guided analysis plan for the UnoPim catalog to identify gaps or optimizations.';

    /**
     * The prompt's arguments.
     *
     * @return array<int, Argument>
     */
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'focus',
                description: 'The area of analysis: "completeness", "consistency", or "optimization".',
                required: false,
            ),
        ];
    }

    /**
     * Handle the prompt request.
     */
    public function handle(Request $request): Response
    {
        $focus = $request->get('focus', 'completeness');

        $content = "You are an expert E-commerce Catalog Consultant. Your task is to analyze the UnoPim catalog.\n\n";
        $content .= '### Analysis Focus: '.ucfirst($focus)."\n\n";
        $content .= "1. First, read the `catalog-schema` resource to understand the system structure.\n";
        $content .= "2. Then, use `search_attributes` and `search_products` to gather specific data points.\n";
        $content .= "3. Provide a report detailing: missing data in required attributes, redundant categories, and suggestions for better attribute grouping.\n\n";
        $content .= 'Please start the analysis now.';

        return Response::text($content);
    }
}
