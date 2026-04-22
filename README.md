# UnoPim MCP Bridge

Empower AI assistants (GitHub Copilot, Claude, Cursor, Windsurf) to interact with your UnoPim catalog, settings, and codebase using the [Model Context Protocol (MCP)](https://modelcontextprotocol.io/).

> The bridge exposes **two transports in one package**:
> | Server | Transport | Best for |
> |---|---|---|
> | **HTTP Agent** | `POST /api/mcp/unopim` (SSE) | Remote AI assistants, PIM workflows |
> | **stdio Agent** | `php artisan mcp:start unopim-dev` | Coding agents (Copilot, Cursor, Claude Code) |

---

## Features

- **Full Catalog CRUD** — Search, get, and upsert products, categories, and attributes with cursor-paginated results and atomic batch operations (up to 50 items per call).
- **Catalog Schema Discovery** — `get_catalog_schema` returns filterable fields, supported operators, and pagination info for every entity type.
- **Settings Management** — Search and upsert channels and locales via unified `search_settings` / `upsert_settings` tools.
- **Developer Tools** — AI-driven file management, safe Artisan/Composer execution, plugin scaffolding, and test generation via the unified `dev_tools` action tool.
- **Dynamic AI Skills** — Drop a `SKILL.md` into `.ai/skills/` and it becomes an MCP tool — no code required.
- **Security** — Production-hardened with Path Traversal protection, Command Whitelisting, Rate Limiting, ACL enforcement, and Audit Logging.
- **Comprehensive Test Suite** — Unit and feature tests covering all tools, services, and security layers.

---

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP         | 8.2+    |
| UnoPim      | 1.0+    |
| Laravel     | 11.0+   |

---

## Installation

```bash
composer require unopim/mcp
php artisan mcp:install
```

Ensure `APP_URL` is set correctly in `.env`.

---

## Connecting to AI Editors / Agents

Register the MCP server in your editor's config file. Both HTTP and stdio transports are supported.

### VS Code / GitHub Copilot — `.vscode/mcp.json`

```jsonc
{
    "servers": {
        "unopim-dev": {
            "command": "php",
            "args": ["artisan", "mcp:start", "unopim-dev"],
            "cwd": "/path/to/your/unopim"
        },
        "unopim-http": {
            "url": "http://127.0.0.1:8000/api/mcp/unopim",
            "type": "http"
        }
    }
}
```

### Cursor — `Preferences > Models > MCP`

Add a new MCP server with the following settings:
- **Type**: `command`
- **Command**: `php artisan mcp:start unopim-dev`

### Claude Code

```bash
claude mcp add unopim-dev -- php artisan mcp:start unopim-dev
```

### Windsurf — `~/.windsurf/mcp.json`

```jsonc
{
    "servers": {
        "unopim-dev": {
            "command": "php",
            "args": ["artisan", "mcp:start", "unopim-dev"],
            "cwd": "/path/to/your/unopim"
        }
    }
}
```

---

## Configuration (`config/mcp.php`)

After running `mcp:install`, you can customize settings in `config/mcp.php`:

```php
return [
    // Require Bearer token for HTTP requests
    'api_auth' => env('MCP_API_AUTH', true),

    // Max requests per minute per tool per client
    'rate_limit' => env('MCP_RATE_LIMIT', 60),

    // Restricted paths for File Manager
    'allowed_paths' => [
        base_path(),
        sys_get_temp_dir(),
    ],

    // Audit logging for destructive operations
    'audit_logging' => env('MCP_AUDIT_LOGGING', true),

    // Dynamic skills directory
    'skills_path' => env('MCP_SKILLS_PATH', base_path('.ai/skills')),

    // Skill caching
    'enable_cache' => env('MCP_ENABLE_CACHE', true),
    'cache_ttl'    => env('MCP_CACHE_TTL', 3600),

    // Media upload restrictions (for future media tools)
    'media' => [
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'csv', 'xlsx'],
        'allowed_mimes'      => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf', 'text/csv', 'text/plain',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
    ],
];
```

---

## Available Tools

### Catalog (8 tools)

| Tool | Description |
|------|-------------|
| `get_catalog_schema` | Returns filterable fields, operators, and pagination info per entity. |
| `search_products` | Cursor-paginated product search with filters (max 100 per page). |
| `get_product` | Fetch full product details by ID or SKU with relationships and completeness. |
| `upsert_products` | Batch create/update products (max 50 per call, atomic transaction). |
| `search_categories` | Cursor-paginated category search with filters. |
| `upsert_categories` | Batch create/update categories (max 50 per call, atomic transaction). |
| `search_attributes` | Cursor-paginated attribute search with filters. |
| `upsert_attributes` | Batch create/update attributes (max 50 per call, atomic transaction). |

### Settings (2 tools)

| Tool | Description |
|------|-------------|
| `search_settings` | Search channels or locales with filters (pass `type`: `channels` or `locales`). |
| `upsert_settings` | Create/update channels or locales (max 50 per call). |

### Developer Tools (2 core + dynamic)

| Tool | Description |
|------|-------------|
| `dev_tools` | Unified action tool with 6 actions: `create_file`, `read_file`, `update_file`, `run_command`, `generate_plugin`, `generate_test`. |
| `run_skill` | Execute a predefined skill from `.ai/skills/` by name. |
| Dynamic Skills | Each `SKILL.md` in `.ai/skills/` is auto-registered as `execute_<skill_name>`. |

### Artisan Commands

| Command | Description |
|---------|-------------|
| `php artisan mcp:install` | Setup OAuth, publish config, clear caches. |
| `php artisan mcp:make plugin <Name>` | Scaffold a complete UnoPim plugin (`connector`, `core-extension`, or `generic`). |
| `php artisan mcp:make test <Package> <Class>` | Generate a Pest test for a class. |
| `php artisan mcp:dev` | Start the STDIO MCP server for local coding agents. |
| `php artisan mcp:inspector <server>` | Launch the MCP Inspector debugger. |

### MCP Resources & Prompts

| Name | Type | Description |
|------|------|-------------|
| `catalog-schema` | Resource | High-level catalog summary (product, category, attribute counts). |
| `analyze-catalog` | Prompt | Guided catalog analysis (completeness, consistency, optimization). |

---

## Query Operators

All search tools support the following filter operators:

| Operator | Description | Example |
|----------|-------------|---------|
| `=` | Equals | `{"field": "status", "operator": "=", "value": "active"}` |
| `!=` | Not equals | `{"field": "type", "operator": "!=", "value": "bundle"}` |
| `IN` | In list | `{"field": "type", "operator": "IN", "value": ["simple", "configurable"]}` |
| `NOT IN` | Not in list | `{"field": "id", "operator": "NOT IN", "value": [1, 2]}` |
| `CONTAINS` | Like %value% | `{"field": "name", "operator": "CONTAINS", "value": "shirt"}` |
| `STARTS WITH` | Like value% | `{"field": "sku", "operator": "STARTS WITH", "value": "PRD"}` |
| `ENDS WITH` | Like %value | `{"field": "sku", "operator": "ENDS WITH", "value": "001"}` |
| `>` | Greater than | `{"field": "price", "operator": ">", "value": 100}` |
| `<` | Less than | `{"field": "stock", "operator": "<", "value": 5}` |

---

## Dynamic Skills

Create a `SKILL.md` file with YAML frontmatter in `.ai/skills/<skill-name>/SKILL.md`:

```markdown
---
name: My Custom Skill
description: Automates a specific workflow
license: MIT
parameters:
  query:
    type: string
    required: true
metadata:
  author: your-name
---

# Instructions

Describe what this skill does and how to use it.
```

The skill is automatically discovered, cached, and registered as an MCP tool named `execute_my_custom_skill`.

---

## Security Policy

The UnoPim MCP Bridge implements multi-layer security:

1. **Request Authentication**: HTTP SSE endpoints default to `auth:api` (OAuth2 via Laravel Passport).
2. **Rate Limiting**: Configurable per-minute limit per tool per client IP (default: 60 req/min).
3. **Path Traversal Guard**: All file operations are jailed within configured `allowed_paths` with `.`/`..` normalization.
4. **Command Whitelisting**: Only `php artisan` and `composer` base commands are allowed. Shell operators (`;`, `&`, `|`, `` ` ``, `$()`, `<`, `>`) are blocked.
5. **ACL Mapping**: Every MCP tool maps to internal UnoPim permissions via `bouncer()`. CLI bypasses ACL for local development.
6. **Audit Logging**: All destructive operations (upsert, create, update) are logged with user ID, IP, tool name, and arguments.
7. **MIME Validation Config**: Media upload restrictions are defined in `config/mcp.php` for future media tool implementations.

---

## Architecture

```
packages/Webkul/MCP/
├── src/
│   ├── Config/mcp.php                    # Configuration (auth, rate limits, paths, media)
│   ├── Console/Commands/                  # Artisan commands (install, make, dev, inspector)
│   ├── Contracts/                         # FileManagerInterface, SkillExecutorInterface
│   ├── DevTools/                          # FileManager, CommandRunner, PluginGenerator, TestGenerator
│   ├── Prompts/Catalog/                   # CatalogAnalysisPrompt
│   ├── Providers/MCPServiceProvider.php   # Service registration, route loading
│   ├── Registry/ToolRegistry.php          # Static registry of 12 core tools
│   ├── Resources/Catalog/                 # CatalogSchemaResource
│   ├── Routes/mcp-routes.php             # HTTP endpoint registration
│   ├── Servers/
│   │   ├── UnoPimAgentServer.php          # Main MCP server (tools, resources, prompts, skills)
│   │   └── Methods/PimCallTool.php        # Auth, rate limiting, ACL, audit proxy
│   ├── Services/                          # SkillExecutor, SkillLoader, SkillParser, UnoPimQueryBuilder
│   └── Tools/
│       ├── BaseMcpTool.php                # Abstract base with error handling
│       ├── Catalog/                       # 8 catalog tools (schema, search, get, upsert)
│       ├── Dev/                           # DevToolsTool, RunSkillTool, DynamicSkillTool
│       └── Settings/                      # SettingSearchTool, SettingUpsertTool
├── tests/
│   ├── Pest.php                           # Pest configuration
│   ├── MCPTestCase.php                    # Base test class
│   ├── Unit/                              # 10 unit test files
│   └── Feature/                           # 11 feature test files
├── composer.json
├── README.md
├── CHANGELOG.md
└── LICENSE.md
```

---

## Testing

Run the full test suite:

```bash
cd packages/Webkul/MCP
../../vendor/bin/pest
```

Or from project root:

```bash
./vendor/bin/pest --filter=MCP
```

The test suite covers:
- **Unit tests**: FileManager, CommandRunner, PluginGenerator, TestGenerator, SkillParser, SkillLoader, SkillExecutor, UnoPimQueryBuilder, PimCallTool, ToolRegistry
- **Feature tests**: Catalog CRUD, Settings management, Bulk operations, Security, DevTools integration, Dynamic Skills, Server registration, Artisan commands

---

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
