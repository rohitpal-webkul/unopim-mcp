# UnoPim MCP Bridge vs. Akeneo MCP

Understanding the differences between the UnoPim MCP Bridge and alternative solutions like Akeneo's MCP implementations is crucial for choosing the right strategy for your PIM development.

## 1. Key Differences

| Feature | UnoPim MCP Bridge (Webkul) | Alternative Akeneo MCPs |
|---|---|---|
| **Ecosystem Integration** | Deeply integrated into Laravel/UnoPim lifecycle. Uses UnoPim's internal repositories and service containers. | Often relies on external REST API calls even when running locally. |
| **Developer Centricity** | Includes `dev_tools` for file system access, command execution, and plugin scaffolding. | Primarily focused on data retrieval and simple updates. |
| **Transport Versatility** | Supports both **stdio** (for local IDEs like Cursor) and **HTTP SSE** (for remote apps) in a single package. | Usually limited to one transport type per implementation. |
| **Extensibility** | Supports **Dynamic Skills** (Markdown-based) that require zero PHP coding to add complex AI workflows. | Typically requires writing new PHP Tool classes for every added capability. |
| **Data Performance** | Uses internal Eloquent optimatizations and cursor-based pagination for large catalog handling. | Limited by REST API rate limits and overhead. |

---

## 2. Advanced Benefits

### Seamless Local Development
Unlike remote-only MCPs, the UnoPim bridge runs directly on your machine via `php artisan mcp:start`. This means your AI assistant has **low-latency access** to your files and database, making it ideal for tasks like:
- Running migrations.
- Generating unit tests for existing files.
- Refactoring internal business logic.

### Capability Discovery
The `get_catalog_schema` tool is a major differentiator. It allows the AI to "self-discover" your specific PIM configuration. If you add a custom attribute in the UI, the AI immediately "sees" it and knows how to filter by it. This eliminates the "hallucination" problem common with generic AI assistants.

### Security and Auditing
While many MCPs are "all-or-nothing", the UnoPim bridge provides granular control:
- **ACL Mapping**: Tools map to actual UnoPim permissions.
- **Audit Logs**: Every destructive act is logged, providing accountability for AI-generated changes.
- **Command Jailing**: Prevents shell injection by whitelisting only safe artisan/composer commands.

---

## 3. Comparative Use Case

**Scenario**: You want to add a "Sustainability Score" attribute and update 100 products.

- **With Akeneo MCP (REST-based)**:
  1. AI searches for products via REST API.
  2. AI asks you to manually create the attribute in Akeneo UI (because it can't).
  3. AI updates products via REST API.
- **With UnoPim MCP Bridge**:
  1. AI uses `upsert_attributes` to create the attribute programmatically.
  2. AI uses `upsert_products` to batch update the scores in a single atomic transaction.
  3. AI uses `run_command` to clear the PIM cache so the changes appear immediately.

This "all-in-one" approach makes UnoPim's implementation significantly more powerful for developers.
