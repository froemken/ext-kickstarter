<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\Kickstarter\Parser;

use FriendsOfTYPO3\Kickstarter\Configuration\ExtConf;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionInformation;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionMappingInformation;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtensionInformationParser
{
    private ExtConf $extConf;

    public function __construct(
        private ExtensionConfiguration $extensionConfiguration
    ) {
        $this->extConf = ExtConf::create($this->extensionConfiguration);
    }

    public function parse(ExtensionMappingInformation $extensionMappingInformation): ExtensionInformation
    {
        $extensionPath = $this->extConf->getExportDirectory() . DIRECTORY_SEPARATOR . $extensionMappingInformation->getExtensionKey() . DIRECTORY_SEPARATOR;
        $composerJson = $this->loadComposerJson($extensionPath);
        return new ExtensionInformation(
            extensionKey: $extensionMappingInformation->getExtensionKey(),
            composerPackageName: $this->getComposerName($composerJson),
            title: '',
            description: '',
            version: '',
            category: '',
            state: '',
            author: '',
            authorEmail: '',
            authorCompany: '',
            namespaceForAutoload: $this->getNamespace($composerJson),
            extensionPath: $extensionPath,
        );
    }

    private function getNamespace(array $composer): string
    {
        $psr4 = $composer['autoload']['psr-4'] ?? null;
        if (!is_array($psr4) || $psr4 === []) {
            throw new \RuntimeException('No PSR-4 namespaces under autoload.', 8648683089);
        }

        // Prefer mapping to "Classes" (TYPO3 convention)
        foreach ($psr4 as $ns => $paths) {
            foreach ((array)$paths as $p) {
                $p = rtrim((string)$p, '/\\');
                if (strcasecmp($p, 'Classes') === 0) {
                    return rtrim($ns, '\\') . '\\';
                }
            }
        }

        // Fallback: exactly one mapping -> use it
        if (count($psr4) === 1) {
            $only = (string)array_key_first($psr4);
            return rtrim($only, '\\') . '\\';
        }

        throw new \RuntimeException('Could not determine a unique TYPO3 namespace (no mapping to "Classes").', 8153121887);
    }

    private function getComposerName(array $composerJson): string
    {
        if (!isset($composerJson['name']) || !is_string($composerJson['name'])) {
            throw new \RuntimeException('No Composer name found for Extension.', 2081629374);
        }
        return strtolower(trim($composerJson['name']));
    }

    private function loadComposerJson(string $extensionPath): array
    {
        $composerJsonFile = $extensionPath . 'composer.json';
        if (!is_file($composerJsonFile)) {
            throw new \RuntimeException('No file found at ' . $composerJsonFile, 9927927278);
        }

        $json = file_get_contents($composerJsonFile);
        if ($json === false) {
            throw new \RuntimeException('Unable to read ' . $composerJsonFile, 5549202368);
        }

        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Invalid composer.json: ' . $e->getMessage(), 1755430632, $e);
        }
    }
}
