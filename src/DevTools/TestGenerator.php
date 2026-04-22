<?php

namespace Webkul\MCP\DevTools;

class TestGenerator
{
    public function __construct(
        protected FileManager $fileManager
    ) {}

    /**
     * Generate a test file for a given class.
     *
     * @param  string  $packageName  e.g. "Webkul/MyPlugin"
     * @param  string  $className  e.g. "Repositories/CredentialRepository"
     */
    public function generate(string $packageName, string $className): string
    {
        $packagePath = "packages/{$packageName}";
        $testPath = "{$packagePath}/tests/Unit/".$className.'Test.php';

        $namespace = str_replace('/', '\\', $packageName).'\\Tests\\Unit';
        if (str_contains($className, '/')) {
            $subNamespace = str_replace('/', '\\', dirname($className));
            $namespace .= '\\'.$subNamespace;
        }

        $baseClassName = basename($className);

        $content = <<<PHP
<?php

namespace {$namespace};

use Tests\TestCase;

class {$baseClassName}Test extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        \$this->assertTrue(true);
    }
}
PHP;

        $this->fileManager->create($testPath, $content);

        return $testPath;
    }
}
