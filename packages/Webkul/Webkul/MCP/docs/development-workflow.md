# Development Workflow with UnoPim MCP

The Model Context Protocol (MCP) bridge fundamentally changes how you develop for UnoPim. Instead of switching between your IDE, Terminal, Database Manager, and Admin UI, you can perform most tasks directly within your AI-powered editor (Cursor, VS Code + Copilot).

## The "AI-First" Development Cycle

### 1. Discovery
Start by understanding your catalog structure. AI assistants can't guess your custom attributes or category hierarchies. Use the `get_catalog_schema` tool first.
- **Query**: "What is the schema for products and what are the filterable attributes?"
- **Outcome**: The AI understands the specific fields it can search and update in your instance.

### 2. Implementation & Scaffolding
Avoid writing boilerplate by hand. Use the interactive `dev_tools`.
- **Action**: `generate_plugin`
- **Example**: "Generate a new core-extension plugin named 'AdvancedPricing' that adds a 'Special Price' field to products."
- **Outcome**: The bridge creates the directory structure, `composer.json`, ServiceProvider, and base classes in `packages/Webkul/AdvancedPricing`.

### 3. Execution & Testing
Run commands without leaving the chat.
- **Action**: `run_command`
- **Example**: "Run `php artisan migrate` and then `php artisan db:seed`."
- **Outcome**: The AI executes the commands securely and reports the output. Note that shell operators are blocked for security—only base artisan/composer commands are allowed.

### 4. Verification
Verify your changes immediately.
- **Action**: `read_file` or `search_products`
- **Example**: "Check if the `wk_products` table migration was successful by reading the migration file and then searching for a test product."

---

## Best Practices for Developers

### Use Sequential Tools
The AI works best when it performs checks before actions.
1. `read_file` to understand existing logic.
2. `generate_test` to create a baseline.
3. `update_file` to make the change.
4. `run_command` (Pest) to verify.

### Atomic Updates
When using `upsert_products` or similar tools, keep batches manageable (max 50). This ensures better error reporting if a single item fails validation.

### Leverage Skills
If you find yourself performing the same sequence of actions (e.g., "Fix PHP 8.1 warnings in all files"), create a **Skill**.
- Drop a `SKILL.md` in `.ai/skills/fix-warnings/`.
- Use it via `execute_fix_warnings`.
- It saves you from re-explaining the instructions every time.

---

## Benefits Summary

| Traditional Workflow | MCP-Powered Workflow |
|---|---|
| Manual directory creation and copy-pasting boilerplate. | Automated scaffolding via `generate_plugin`. |
| Switching to UI to verify data updates. | Instant verification via `get_product` or `search_products`. |
| Manual CLI context switching for migrations/tests. | In-chat command execution via `run_command`. |
| Trial and error with attribute SKUs/Codes. | Guided discovery via `get_catalog_schema`. |
