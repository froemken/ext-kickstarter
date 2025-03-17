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
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @param iterable<ExtensionCreatorInterface> $creators
 */
class ExtensionCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly iterable $creators,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'extension_key',
            InputArgument::OPTIONAL,
            'Provide the extension key you want to extend.',
        );
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

        $extensionInformation = $this->askForExtensionInformation(
            $io,
            $this->askForExtensionKey($io, $input->getArgument('extension_key'))
        );

        foreach ($this->creators as $creator) {
            $creator->create($extensionInformation);
        }

        return Command::SUCCESS;
    }

    private function askForExtensionInformation(SymfonyStyle $io, string $extensionKey): ExtensionInformation
    {
        // We are creating a new extension, so remove previous exported extension
        $extensionPath = $this->createExtensionPath($extensionKey, true);
        $composerPackageName = $this->askForComposerPackageName($io);
        $title = (string)$io->ask(
            'Please provide the title of your extension',
            ucwords(preg_replace('/_/', ' ', $extensionKey))
        );
        $description = (string)$io->ask('Please provide a short description for your extension');
        $version = $this->askForVersion($io);
        $category = (string)$io->choice(
            'Please provide the category for your extension',
            ['be', 'module', 'fe', 'plugin', 'misc', 'services', 'templates', 'example', 'doc', 'distribution'],
            'plugin'
        );
        $state = (string)$io->choice(
            'Please choose the state of your extension',
            ['alpha', 'beta', 'stable', 'experimental', 'test', 'excludeFromUpdates'],
            'alpha'
        );
        $author = (string)$io->ask('Please enter the author name');
        $authorEmail = $this->askForEmail($io);
        $authorCompany = (string)$io->ask('Provide the author\'s company');
        $namespacePrefix = (string)$io->ask(
            'Please provide the namespace prefix to use for "autoload" in composer.json',
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

    private function askForVersion(SymfonyStyle $io): string
    {
        do {
            $version = $io->ask('Please provide the version for your extension', '0.0.1');

            if (!preg_match('#^(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$#', $version)) {
                $io->error('Invalid version string. The version must match a specific pattern (see: https://semver.org/#is-there-a-suggested-regular-expression-regex-to-check-a-semver-string)');
                $validVersion = false;
            } else {
                $validVersion = true;
            }
        } while (!$validVersion);

        return $version;
    }

    private function askForEmail(SymfonyStyle $io): string
    {
        do {
            $email = $io->ask('Provide the author\'s email');

            if (!GeneralUtility::validEmail($email)) {
                $io->error('You have entered an invalid email address.');
                $validEmail = false;
            } else {
                $validEmail = true;
            }
        } while (!$validEmail);

        return $email;
    }

    private function convertComposerPackageNameToNamespacePrefix(string $composerPackageName): string
    {
        return implode(
                '\\\\',
                array_map(
                    fn($part) => str_replace(
                        ['-', '_', '.'],
                        '',
                        ucwords($part, '-_ .')
                    ),
                    explode('/', $composerPackageName)
                )
            ) . '\\\\';
    }
}
