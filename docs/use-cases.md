# UnoPim MCP Bridge: Use Cases

The UnoPim MCP Bridge enables a wide range of use cases by connecting AI assistants directly to the PIM's internal capabilities. Here are some key scenarios organized by user role.

## 1. For Catalog Managers & Data Entry
Catalog managers can use AI to perform complex data operations that would otherwise require manual entry or custom import scripts.

### Bulk Attribute Enrichment
- **Scenario**: You have 500 new products with minimal descriptions.
- **Workflow**: 
  - "Search for products without descriptions created in the last 24 hours."
  - "Based on the product name and current attributes, generate a 200-word SEO-friendly description for each."
  - "Upsert the descriptions back to the catalog."
- **Benefit**: Saves hours of manual writing and copying.

### Mass Category Re-assignment
- **Scenario**: You are restructuring your catalog and need to move all 'Winter Wear' products to a new 'Seasonal > Winter' category.
- **Workflow**:
  - "Find all products in the 'Winter Wear' category."
  - "Update their category mapping to 'Seasonal > Winter' and remove the old mapping."
- **Benefit**: Handles complex relational updates across hundreds of records instantly.

### Consistency Checks
- **Scenario**: Ensuring all products in the 'Electronics' category have a 'Voltage' attribute filled.
- **Workflow**:
  - "Analyze products in category 'Electronics' and list those missing the 'Voltage' attribute."
- **Benefit**: Ensures high data quality and completeness for downstream channels.

---

## 2. For Developers & Technical Teams
Developers can leverage the bridge to accelerate development and maintenance of the UnoPim platform itself.

### Scaffolding New Connectors
- **Scenario**: You need to build a new integration for a shipping provider.
- **Workflow**:
  - `dev_tools` action `generate_plugin` with type `connector`.
  - "Scaffold a new connector plugin named 'ShipStation' with a basic configuration page."
- **Benefit**: Generates PSR-12 compliant boilerplate, service providers, and routes in seconds.

### Rapid Prototyping
- **Scenario**: Testing how a new attribute type behaves in the UI or API.
- **Workflow**:
  - "Create a new select-type attribute named 'Fabric Material' with options: Cotton, Silk, Wool."
  - "Assign it to the 'Clothing' attribute group."
- **Benefit**: No need to navigate multiple admin screens; define it in natural language.

### Automated Troubleshooting
- **Scenario**: A product is not appearing in the frontend channel.
- **Workflow**:
  - "Check product SKU 'PHONE-001' completeness for channel 'Default'."
  - "If completeness is < 100%, list the missing required attributes."
- **Benefit**: Instant diagnostic reports without manually checking completeness tables.

---

## 3. For Store Owners & Business Analysts
Business users can get high-level insights and manage global settings without technical knowledge.

### Multi-Channel Sync Audits
- **Scenario**: Verifying that price updates have reached both Shopify and WooCommerce channels.
- **Workflow**:
  - "Compare product pricing for channel 'Shopify' and 'WooCommerce' for the top 50 selling items."
- **Benefit**: Quick verification of data consistency across synchronized stores.

### Locale & Channel Expansion
- **Scenario**: Preparing the store for a new French market.
- **Workflow**:
  - `search_settings` for locales to see if 'fr_FR' exists.
  - "Add the French locale if missing and enable it for the 'B2C' channel."
- **Benefit**: Rapid configuration of global settings.
