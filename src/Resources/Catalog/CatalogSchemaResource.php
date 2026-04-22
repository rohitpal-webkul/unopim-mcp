<?php

namespace Webkul\MCP\Resources\Catalog;

use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class CatalogSchemaResource extends Resource
{
    /**
     * The resource's name.
     */
    protected string $name = 'catalog-schema';

    /**
     * The resource's title.
     */
    protected string $title = 'UnoPim Catalog Schema';

    /**
     * The resource's description.
     */
    protected string $description = 'A high-level overview of the UnoPim catalog structure, including attributes and categories.';

    /**
     * The resource's base URI.
     */
    protected string $uri = 'uno://catalog/schema';

    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Handle the resource request.
     */
    public function handle(): Response
    {
        $attributesCount = $this->attributeRepository->count();
        $categoriesCount = $this->categoryRepository->count();
        $productsCount = $this->productRepository->count();

        $content = "# UnoPim Catalog Summary\n\n";
        $content .= "This resource provides the overall structure of the UnoPim catalog.\n\n";

        $content .= "## Statistics\n";
        $content .= "- **Total Products**: {$productsCount}\n";
        $content .= "- **Total Categories**: {$categoriesCount}\n";
        $content .= "- **Total Attributes**: {$attributesCount}\n\n";

        $content .= "## Next Steps\n";
        $content .= "- Use `search_attributes` to see detailed attribute definitions.\n";
        $content .= "- Use `search_categories` to explore the category tree.\n";
        $content .= "- Use `get_catalog_schema` to discover filterable fields and query operators.\n";

        return Response::text($content);
    }
}
