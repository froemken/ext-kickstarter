<?php

namespace Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\ExtensionCreatorService;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ExtensionCreatorServiceTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'ext_kickstarter',
    ];

    protected array $coreExtensionsToLoad = [
        'install',
    ];

    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/extkickstarter_service_' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->tempDir);
        parent::tearDown();
    }

    #[Test]
    #[DataProvider('extensionCreationProvider')]
    public function testItCreatesExpectedExtensionFiles(
        ExtensionInformation $extensionInfo,
        string $generatedPath,
        string $expectedDir,
        array $expectedFiles
    ): void {
        mkdir($extensionInfo->getExtensionPath(), 0777, true);

        $creatorService = $this->get(ExtensionCreatorService::class);
        $creatorService->create($extensionInfo);

        self::assertDirectoryExists($generatedPath);

        // ✅ Check all expected files dynamically
        foreach ($expectedFiles as $file) {
            self::assertFileExists($generatedPath . '/' . $file, "Missing expected file: $file");
        }

        // ✅ Compare generated files with fixtures
        $this->assertDirectoryEquals($expectedDir, $generatedPath);
    }

    public static function extensionCreationProvider(): array
    {
        $tempDir = sys_get_temp_dir() . '/extkickstarter_service_test';
        $generatedPath = $tempDir . '/my_extension';
        $extensionInfo = new ExtensionInformation(
            'my_extension',
            'my-vendor/my-extension',
            'My Extension',
            'This is a test extension',
            '1.0.0',
            'plugin',
            'stable',
            'John Doe',
            'john@example.com',
            'MyCompany',
            'Vendor\\MyExtension\\',
            $generatedPath . '/'
        );

        return [
            'default extension' => [
                $extensionInfo,
                $generatedPath,
                __DIR__ . '/Fixtures/expected_extension',
                ['ext_emconf.php', 'README.md'], // expected files are now part of provider
            ],
        ];
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }
        rmdir($dir);
    }

    private function shouldUpdateBaseline(): bool
    {
        // Environment variable support
        if (getenv('UPDATE_BASELINE') === '1') {
            return true;
        }

        return false;
    }

    private function assertDirectoryEquals(string $expectedDir, string $actualDir): void
    {
        if ($this->shouldUpdateBaseline()) {
            $this->copyDirectory($actualDir, $expectedDir);
            self::markTestSkipped('Baseline updated: expected fixtures were overwritten with new output.');
        }

        // Normal comparison when not updating baseline
        $expectedFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($expectedDir));
        foreach ($expectedFiles as $file) {
            if ($file->isDir()) {
                continue;
            }
            $relativePath = str_replace($expectedDir, '', $file->getPathname());
            $actualFile = $actualDir . $relativePath;

            self::assertFileExists($actualFile, sprintf('Missing file: %s', $relativePath));
            self::assertSame(
                trim(file_get_contents($file->getPathname())),
                trim(file_get_contents($actualFile)),
                sprintf('File contents differ for: %s', $relativePath)
            );
        }
    }

    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            $target = $destination . '/' . $file->getBasename();

            if ($file->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0777, true);
                }
            } else {
                copy($file->getPathname(), $target);
            }
        }
    }
}
