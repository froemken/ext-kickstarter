<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

use Symfony\Component\Console\Style\SymfonyStyle;

trait AskForExtensionKeyTrait
{
    private function askForExtensionKey(SymfonyStyle $io, string $defaultExtensionKey = null): string
    {
        $io->text([
            'Building a new TYPO3 extension needs a unique identifier, the so called extension key. See:',
            'https://docs.typo3.org/m/typo3/reference-coreapi/12.4/en-us/ExtensionArchitecture/BestPractises/ExtensionKey.html'
        ]);

        do {
            $extensionKey = (string)$io->ask('Please provide the key for your extension', $defaultExtensionKey);
            $length = mb_strlen($extensionKey);

            if ($length < 3 || $length > 30) {
                $io->error('Extension key length must be between 3 and 30 characters');
                $defaultExtensionKey = $this->tryToCorrectExtensionKey($extensionKey);
                $validExtensionKey = false;
            } elseif (!preg_match('/^[a-z][a-z0-9_]*$/', $extensionKey)) {
                $io->error('Extension key can only start with a lowercase letter and contain lowercase letters, numbers, or underscores');
                $defaultExtensionKey = $this->tryToCorrectExtensionKey($extensionKey);
                $validExtensionKey = false;
            } elseif (preg_match('/^[_]|[_]$/', $extensionKey)) {
                $io->error('Extension key cannot start or end with an underscore');
                $defaultExtensionKey = $this->tryToCorrectExtensionKey($extensionKey);
                $validExtensionKey = false;
            } elseif (preg_match('/^(tx|user_|pages|tt_|sys_|ts_language|csh_)/', $extensionKey)) {
                $io->error('Extension key cannot start with reserved prefixes such as tx, user_, pages, tt_, sys_, ts_language, or csh_');
                $defaultExtensionKey = $this->tryToCorrectExtensionKey($extensionKey);
                $validExtensionKey = false;
            } else {
                $validExtensionKey = true;
            }
        } while (!$validExtensionKey);

        return $extensionKey;
    }

    private function tryToCorrectExtensionKey(string $givenExtensionKey): string
    {
        // Lower case all chars
        $cleanedExtensionKey = strtolower($givenExtensionKey);

        // Change "-" to "_". Migrates package names to extension key
        $cleanedExtensionKey = str_replace('-', '_', $cleanedExtensionKey);

        // Remove invalid chars
        $cleanedExtensionKey = preg_replace('/[^a-z0-9_]/', '', $cleanedExtensionKey);

        // Remove leading numbers
        $cleanedExtensionKey = preg_replace('/^[0-9]+/', '', $cleanedExtensionKey);

        // Remove leading and trailing "_"
        return trim($cleanedExtensionKey, '_');
    }
}