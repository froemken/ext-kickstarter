<?php

namespace StefanFroemken\ExtKickstarter\Command\Question;

use StefanFroemken\ExtKickstarter\Configuration\ExtConf;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Registry;

readonly class ChoseExtensionKeyQuestion
{
    public function __construct(
        private Registry $registry,
        private ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function ask(SymfonyStyle $io, ?string $defaultExtensionKey = null): ?string
    {
        $path = ExtConf::create($this->extensionConfiguration)->getExportDirectory();
        $lastExtension = $defaultExtensionKey ?? $this->registry->get(ExtConf::EXT_KEY, ExtConf::LAST_EXTENSION_REGISTRY_KEY);
        $availableExtensions = $this->getAvailableExtensions($path);
        $io->text([
            'Building a new TYPO3 extension needs a unique identifier, the so called extension key. See:',
            'https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/BestPractises/ExtensionKey.html',
        ]);

        if ($availableExtensions !== []) {
            $extensionKey = $io->choice(
                'Which extension should be modified? ',
                $availableExtensions,
                $lastExtension,
            );
            $this->registry->set(ExtConf::EXT_KEY, ExtConf::LAST_EXTENSION_REGISTRY_KEY, $extensionKey);
        } else {
            $io->error('No extensions found at path ' . $path);
            $io->info('Create an extension using command make:extension or make:site-package first. ');
            die();
        }

        return $extensionKey;
    }

    private function getAvailableExtensions(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $extensions = [];
        $directories = scandir($path);

        foreach ($directories as $dir) {
            if ($dir === '.') {
                continue;
            }
            if ($dir === '..') {
                continue;
            }
            $fullPath = $path . DIRECTORY_SEPARATOR . $dir;

            // Check if it is a directory and has a composer.json
            if (is_dir($fullPath) && file_exists($fullPath . DIRECTORY_SEPARATOR . 'composer.json')) {
                $extensions[] = $dir;
            }
        }

        return $extensions;
    }
}
