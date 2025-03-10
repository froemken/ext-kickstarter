<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Creator\Extension\ExtensionCreatorInterface;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Model\Node;
use StefanFroemken\ExtKickstarter\Traits\GetExtensionPathTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @param iterable<ExtensionCreatorInterface> $creators
 */
class ExtensionCommand extends Command
{
    use GetExtensionPathTrait;

    public function __construct(
        private readonly iterable $creators,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Welcome to the TYPO3 Extension Builder');

        $io->text([
            'We are here to assist you in creating a new TYPO3 extension.',
            'Now, we will ask you a few questions to customize the extension according to your needs.',
            'Please take your time to answer them.',
        ]);

        $extensionInformation = $this->askForExtensionInformation($io, $this->askForExtensionKey($io));

        foreach ($this->creators as $creator) {
            $creator->create($extensionInformation);
        }

        return Command::SUCCESS;
    }

    private function askForExtensionInformation(SymfonyStyle $io, string $extensionKey): ExtensionInformation
    {
        // We are creating a new extension, so remove previous exports
        $extensionPath = $this->getExtensionPath($extensionKey, true);
        $composerPackageName = $this->askForComposerPackageName($io);
        $title = (string)$io->ask(
            'Please provide the title of your extension',
            ucwords(preg_replace('/_/', ' ', $extensionKey))
        );
        $description = (string)$io->ask('Please provide a short description for your extension');
        $version = (string)$io->ask('Please provide the version for your extension', '0.0.0');
        $category = (string)$io->choice(
            'Please provide the category for your extension',
            ['be', 'module', 'fe', 'plugin', 'misc', 'services', 'templates', 'example', 'doc', 'distribution'],
            'plugin'
        );
        $state = (string)$io->choice(
            'Please choose the state of your extension',
            ['alpha', 'beta', 'stable', 'experimental', 'test', 'obsolete', 'excludeFromUpdates'],
            'alpha'
        );
        $author = (string)$io->ask('Please enter the author name');
        $authorEmail = (string)$io->ask('Provide the author\'s email');
        $authorCompany = (string)$io->ask('Provide the author\'s company');
        $namespacePrefix = (string)$io->ask(
            'Please provide a short description for your extension',
            $this->convertComposerPackageNameToNamespacePrefix($composerPackageName),
        );

        return new ExtensionInformation(
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
            $namespacePrefix,
            $extensionPath,
        );
    }

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
            } else {
                $validExtensionKey = true;
            }
        } while (!$validExtensionKey);

        return $extensionKey;
    }

    private function askForComposerPackageName(SymfonyStyle $io): string
    {
        do {
            $composerPackageName = $io->ask('Please provide the composer name for your extension (my-vendor/my-extension)');

            if (!preg_match('#^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9](([_.]|-{1,2})?[a-z0-9]+)*$#', $composerPackageName)) {
                $io->error('Invalid composer package name. Package name must follow a specific pattern (see: https://getcomposer.org/doc/04-schema.md#name)');
                $validComposerPackageName = false;
            } else {
                $validComposerPackageName = true;
            }
        } while (!$validComposerPackageName);

        return $composerPackageName;
    }

    private function convertComposerPackageNameToNamespacePrefix(string $composerPackageName): string
    {
        return implode(
                '\\',
                array_map(
                    fn($part) => str_replace(
                        ['-', '_', '.'],
                        '',
                        ucwords($part, '-_ .')
                    ),
                    explode('/', $composerPackageName)
                )
            ) . '\\';
    }
}
