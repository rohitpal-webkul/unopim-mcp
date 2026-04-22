<?php

use Illuminate\Support\Facades\File;
use Webkul\MCP\DevTools\FileManager;
use Webkul\MCP\DevTools\TestGenerator;

beforeEach(function () {
    $this->fileManager = new FileManager;
    $this->generator = new TestGenerator($this->fileManager);
});

afterEach(function () {
    // Cleanup generated test files
    $paths = [
        base_path('packages/Webkul/TestPkg/tests/Unit/Services/PaymentServiceTest.php'),
        base_path('packages/Webkul/TestPkg/tests/Unit/MyClassTest.php'),
        base_path('packages/Webkul/TestPkg/tests'),
        base_path('packages/Webkul/TestPkg'),
    ];

    foreach ($paths as $path) {
        if (File::isFile($path)) {
            File::delete($path);
        }
    }

    // Clean up empty directories
    $dir = base_path('packages/Webkul/TestPkg/tests/Unit/Services');
    if (is_dir($dir)) {
        File::deleteDirectory(base_path('packages/Webkul/TestPkg'));
    }

    $dir = base_path('packages/Webkul/TestPkg/tests/Unit');
    if (is_dir($dir)) {
        File::deleteDirectory(base_path('packages/Webkul/TestPkg'));
    }

    if (is_dir(base_path('packages/Webkul/TestPkg'))) {
        File::deleteDirectory(base_path('packages/Webkul/TestPkg'));
    }
});

it('generates a test file for a simple class name', function () {
    $result = $this->generator->generate('Webkul/TestPkg', 'MyClass');

    expect($result)->toBe('packages/Webkul/TestPkg/tests/Unit/MyClassTest.php');
    expect(File::exists(base_path($result)))->toBeTrue();

    $content = File::get(base_path($result));
    expect($content)->toContain('class MyClassTest extends TestCase');
    expect($content)->toContain('namespace Webkul\\TestPkg\\Tests\\Unit');
    expect($content)->toContain('function test_example');
});

it('generates a test file for a nested class name', function () {
    $result = $this->generator->generate('Webkul/TestPkg', 'Services/PaymentService');

    expect($result)->toBe('packages/Webkul/TestPkg/tests/Unit/Services/PaymentServiceTest.php');
    expect(File::exists(base_path($result)))->toBeTrue();

    $content = File::get(base_path($result));
    expect($content)->toContain('namespace Webkul\\TestPkg\\Tests\\Unit\\Services');
    expect($content)->toContain('class PaymentServiceTest extends TestCase');
});

it('throws exception if test file already exists', function () {
    // Create the first time
    $this->generator->generate('Webkul/TestPkg', 'MyClass');

    // Try to create again
    expect(fn () => $this->generator->generate('Webkul/TestPkg', 'MyClass'))
        ->toThrow(RuntimeException::class, 'already exists');
});
