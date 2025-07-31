<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Command\Input\Question\ComposerNameQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\Question\EmailQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\Question\NamespaceQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\Question\VersionQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\QuestionFactory;
use StefanFroemken\ExtKickstarter\Creator\Extension\ExtensionCreatorInterface;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\ExtensionCreatorService;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @param iterable<ExtensionCreatorInterface> $creators
 */
class ExtensionCommand extends Command
{
    use CreatorInformationTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly ExtensionCreatorService $extensionCreatorService,
        private readonly QuestionFactory $questionFactory,
        private readonly Registry $registry,
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

        $io->title('Questions to build a new TYPO3 Extension');

        $extensionKey = (string)$this->questionFactory
            ->getQuestion('extension_key', $input, $output)
            ->ask(default: (string)$input->getArgument('extension_key'));

        $extensionInformation = $this->askForExtensionInformation($io, $extensionKey);
        $extensionInformation = $this->askForExtensionInformation(
            $io,
            $this->askForExtensionKey($this->registry, $io, $input->getArgument('extension_key'))
        );
        $extensionInformation = $this->askForExtensionInformation($input, $output, $io, $extensionKey);

        $this->extensionCreatorService->create($extensionInformation);

        $path = $extensionInformation->getExtensionPath();

        $io->success(sprintf('The extension was saved to path %s', $path));
        $this->printInstallationInstructions($io, $path, $extensionInformation);

        $this->printCreatorInformation($extensionInformation->getCreatorInformation(), $io);

        return Command::SUCCESS;
    }

    public function printInstallationInstructions(SymfonyStyle $io, string $path, ExtensionInformation $extensionInformation): void
    {
        if (Environment::isComposerMode()) {
            if (str_contains($path, 'typo3temp')) {
                $io->writeln([
                    '<info>Move the extension to a directory outside the web root (e.g., "packages").</info>',
                    '',
                    'Then add the path to your composer.json using:',
                    sprintf(
                        '<comment>composer config repositories.%1$s path packages/%1$s</comment>',
                        $extensionInformation->getExtensionKey()
                    ),
                    '',
                ]);
            }

            $io->writeln([
                '<info>Install the extension with Composer using:</info>',
                sprintf(
                    '<comment>composer req %s:@dev</comment>',
                    $extensionInformation->getComposerPackageName()
                ),
                '',
            ]);
            return;
        }

        // Classic mode
        if (!str_contains($path, 'typo3conf/ext')) {
            $io->writeln([
                '<info>Move the extension to the directory "typo3conf/ext/".</info>',
                '',
            ]);
        }

        $io->writeln([
            '<info>Activate the extension in the TYPO3 backend under:</info>',
            '<comment>Admin Tools → Extension Manager</comment>',
            sprintf(
                '<comment>(%s)</comment>',
                $extensionInformation->getComposerPackageName()
            ),
            '',
        ]);
    }

    private function askForExtensionInformation(InputInterface $input, OutputInterface $output, SymfonyStyle $io, string $extensionKey): ExtensionInformation
    {
        $io->info([
            'The extension will be exported to directory: ' . $this->getExtensionPath($extensionKey),
            'You can configure the export directory in extension settings (available in InstallTool)',
        ]);

        // We are creating a new extension, so remove previous exported extension after user confirmation
        if (is_dir($this->getExtensionPath($extensionKey))) {
            $io->warning([
                'There is already an extension at location: "' . $this->getExtensionPath($extensionKey) . '".',
                'While creating a new extension, we will remove the previous extension and create a new one.',
            ]);
            $confirmRemoval = $io->confirm(
                'Please confirm, that you want to remove the previous extension and create a new one.',
                false
            );
            if ($confirmRemoval === false) {
                die();
            }
        }

        $composerPackageName = (string)$this->questionFactory
            ->getQuestion(ComposerNameQuestion::ARGUMENT_NAME, $input, $output)
            ->ask();

        $io->text([
            'The title of the extension will be used to identify the extension much easier',
            'in the TYPO3 ExtensionManager and also in TER (https://extensions.typo3.org)',
        ]);
        $title = (string)$io->ask(
            'Please provide the title of your extension',
            ucwords(preg_replace('/_/', ' ', $extensionKey))
        );

        $io->text([
            'The description describes your new extension in short. It should not exceed more than two sentences.',
            'This will help users in TER (https://extensions.typo3.org) to get the point of what your extension does/provides',
        ]);
        $description = (string)$io->ask('Description');

        $version = (string)$this->questionFactory
            ->getQuestion(VersionQuestion::ARGUMENT_NAME, $input, $output)
            ->ask();

        $io->text([
            'The category is used to group your extension in the TYPO3 ExtensionManager.',
            'See: https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/FileStructure/ExtEmconf.html#confval-ext-emconf-category',
        ]);
        $category = (string)$io->choice(
            'Category',
            [
                'be',
                'module',
                'fe',
                'plugin',
                'misc',
                'services',
                'templates',
                'example',
                'doc',
                'distribution',
            ],
            'plugin'
        );

        $io->text([
            'The state is used to determine the visibility of your extension in the TYPO3 ExtensionManager.',
            'Link: https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/FileStructure/ExtEmconf.html#confval-ext-emconf-state',
        ]);
        $state = (string)$io->choice(
            'State',
            [
                'alpha',
                'beta',
                'stable',
                'experimental',
                'test',
                'excludeFromUpdates',
            ],
            'alpha'
        );

        $io->text([
            'Who is the author of this extension?',
            'Please enter the name of that person with first- and lastname.',
            'Do not enter company. It will be asked some questions later.',
        ]);
        $author = (string)$io->ask('Author name');

        $authorEmail = (string)$this->questionFactory
            ->getQuestion(EmailQuestion::ARGUMENT_NAME, $input, $output)
            ->ask();

        $io->text([
            'Enter the company name of the author (see above)',
            'As a private/personnel developer you can leave that blank.',
        ]);
        $authorCompany = (string)$io->ask('Company name');

        $namespacePrefix = (string)$this->questionFactory
            ->getQuestion(NamespaceQuestion::ARGUMENT_NAME, $input, $output)
            ->ask($this->convertComposerPackageNameToNamespacePrefix($composerPackageName));

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
            $this->createExtensionPath($extensionKey, true),
        );
    }

    private function convertComposerPackageNameToNamespacePrefix(string $composerPackageName): string
    {
        return implode(
            '\\',
            array_map(
                fn($part): string|array => str_replace(
                    [
                        '-',
                        '_',
                        '.',
                    ],
                    '',
                    ucwords($part, '-_ .')
                ),
                explode('/', $composerPackageName)
            )
        ) . '\\';
    }
}
