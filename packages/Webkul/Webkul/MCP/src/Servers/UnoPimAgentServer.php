<?php

namespace Webkul\MCP\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Contracts\Transport;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Tool;
use Webkul\MCP\Prompts\Catalog\CatalogAnalysisPrompt;
use Webkul\MCP\Registry\ToolRegistry;
use Webkul\MCP\Resources\Catalog\CatalogSchemaResource;
use Webkul\MCP\Servers\Methods\PimCallTool;
use Webkul\MCP\Services\SkillLoader;
use Webkul\MCP\Tools\Dev\DynamicSkillTool;

class UnoPimAgentServer extends Server
{
    /**
     * The name of the MCP server.
     */
    protected string $name = 'UnoPim MCP Agent';

    /**
     * The version of the MCP server.
     */
    protected string $version = '1.0.0';

    /**
     * The instructions for AI clients connecting to this server.
     */
    protected string $instructions = <<<'MARKDOWN'
        This MCP server connects AI coding assistants directly into the UnoPim PIM platform.

        ### Capabilities
        - **Catalog Discovery**: Use `get_catalog_schema` to understand filterable fields, operators, and pagination rules.
        - **Product Management**: Use `search_products` to find products, `get_product` for details, and `upsert_products` to create/update (batch up to 50).
        - **Category Management**: Use `search_categories` to browse and `upsert_categories` to create/update categories.
        - **Attribute Management**: Use `search_attributes` to explore attributes and `upsert_attributes` to create/update them.
        - **Settings Management**: Use `search_settings` and `upsert_settings` to manage channels and locales.
        - **Developer Tools**: Use `dev_tools` for file management, command execution, and code generation. Use `run_skill` to execute predefined development skills.
        - **Dynamic Skills**: Custom skills loaded from `.ai/skills/` are registered as additional tools.

        ### Guidelines
        - Always start with `get_catalog_schema` to discover the catalog structure before querying.
        - All search tools support generic filters with operators: `=`, `!=`, `IN`, `NOT IN`, `CONTAINS`, `STARTS WITH`, `ENDS WITH`, `>`, `<`.
        - All search tools use cursor-based pagination (max 100 per page).
        - Use `dev_tools` with action `generate_plugin` to scaffold new extensions.
        - Use `dev_tools` with action `run_command` for artisan/composer commands.
        - Access the `catalog-schema` resource for a high-level catalog summary.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [
        CatalogSchemaResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<Prompt>>
     */
    protected array $prompts = [
        CatalogAnalysisPrompt::class,
    ];

    /**
     * Initialize the server and tools.
     */
    public function __construct(
        Transport $transport,
        protected SkillLoader $skillLoader
    ) {
        parent::__construct($transport);

        $this->tools = ToolRegistry::tools();
        $this->loadDynamicSkills();
    }

    protected function boot(): void
    {
        $this->methods['tools/call'] = PimCallTool::class;
    }

    /**
     * Load dynamic skills from .ai/skills as tools.
     */
    protected function loadDynamicSkills(): void
    {
        $skills = $this->skillLoader->all();

        foreach ($skills as $skill) {
            $this->tools[] = app(DynamicSkillTool::class, ['skillData' => $skill]);
        }
    }
}
