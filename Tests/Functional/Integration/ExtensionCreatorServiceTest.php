<?php

namespace Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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

    private function getTrimmedFileContent(string $actualFile): string
    {
        $content = file_get_contents($actualFile);
        if ($content === false) {
            return '';
        }
        return trim($content);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    #[Test]
    #[DataProvider('extensionCreationProvider')]
    public function testItCreatesExpectedExtensionFiles(
        string $extensionKey,
        string $composerPackageName,
        string $title,
        string $description,
        string $version,
        string $category,
        string $state,
        string $author,
        string $authorEmail,
        string $authorCompany,
        string $namespaceForAutoload,
        string $expectedDir,
        array $expectedFiles,
    ): void {
        // Build paths based on $this->instancePath
        $extensionPath = $this->instancePath . '/' . $extensionKey . '/';
        $generatedPath = $this->instancePath . '/' . $extensionKey . '/';

        // Build the ExtensionInformation object here
        $extensionInfo = new ExtensionInformation(
            $extensionKey,
            $composerPackageName,
            $title,
            $description,
            $version,
            $category,
            $state,
            $author,
            $authorEmail,
            $authorCompany,
            $namespaceForAutoload,
            $extensionPath
        );

        mkdir($extensionInfo->getExtensionPath(), 0777, true);

        $creatorService = $this->get(ExtensionCreatorService::class);
        $creatorService->create($extensionInfo);

        self::assertDirectoryExists($generatedPath);

        // Check all expected files dynamically
        foreach ($expectedFiles as $file) {
            self::assertFileExists($generatedPath . '/' . $file, 'Missing expected file: ' . $file);
        }

        // Compare generated files with fixtures
        $this->assertDirectoryEquals($expectedDir, $generatedPath);
    }

    public static function extensionCreationProvider(): array
    {
        return [
            'default extension' => [
                'extensionKey' => 'my_extension',
                'composerPackageName' => 'my-vendor/my-extension',
                'title' => 'My Extension',
                'description' => 'This is a test extension',
                'version' => '1.0.0',
                'category' => 'plugin',
                'state' => 'stable',
                'author' => 'John Doe',
                'authorEmail' => 'john@example.com',
                'authorCompany' => 'MyCompany',
                'namespaceForAutoload' => 'Vendor\\MyExtension\\',
                'expectedDir' => __DIR__ . '/Fixtures/expected_extension',
                'expectedFiles' => ['ext_emconf.php', 'README.md'],
            ],
        ];
    }

    private function shouldUpdateBaseline(): bool
    {
        // Environment variable support
        return getenv('UPDATE_BASELINE') === '1';
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
                $this->getTrimmedFileContent($file->getPathname()),
                $this->getTrimmedFileContent($actualFile),
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

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
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
