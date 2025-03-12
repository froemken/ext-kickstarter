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
    private function askForExtensionKey(SymfonyStyle $io): string
    {
        do {
            $extensionKey = (string)$io->ask('Please provide the key for your extension');
            $length = mb_strlen($extensionKey);

            if ($length < 3 || $length > 30) {
                $io->error('Extension key length must be between 3 and 30 characters');
                $validExtensionKey = false;
            } elseif (!preg_match('/^[a-z][a-z0-9_]*$/', $extensionKey)) {
                $io->error('Extension key can only start with a lowercase letter and contain lowercase letters, numbers, or underscores');
                $validExtensionKey = false;
            } elseif (preg_match('/^[_]|[_]$/', $extensionKey)) {
                $io->error('Extension key cannot start or end with an underscore');
                $validExtensionKey = false;
            } elseif (preg_match('/^(tx|user_|pages|tt_|sys_|ts_language|csh_)/', $extensionKey)) {
                $io->error('Extension key cannot start with reserved prefixes such as tx, user_, pages, tt_, sys_, ts_language, or csh_');
                $validExtensionKey = false;
            } elseif (preg_match('/^(tx|user_|pages|tt_|sys_|ts_language|csh_)/', $extensionKey)) {
                $io->error('Extension key cannot start with reserved prefixes such as tx, user_, pages, tt_, sys_, ts_language, or csh_');
                $validExtensionKey = false;
            } else {
                $validExtensionKey = true;
            }
        } while (!$validExtensionKey);

        return $extensionKey;
    }
}