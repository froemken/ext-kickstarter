<?php

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question;

use FriendsOfTYPO3\Kickstarter\Configuration\ExtConf;
use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Registry;

#[AutoconfigureTag('ext-kickstarter.command.extension.question')]
readonly class ChooseExtensionKeyQuestion extends AbstractQuestion
{
    public const ARGUMENT_NAME = 'choose_extension';

    private const QUESTION = [
        'Which extension should be modified?',
    ];

    private const DESCRIPTION = [
        'Building a new TYPO3 extension needs a unique identifier, the so called extension key. See:',
        'https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/BestPractises/ExtensionKey.html',
    ];

    public function getArgumentName(): string
    {
        return self::ARGUMENT_NAME;
    }

    public function __construct(
        private Registry $registry,
        private ExtensionConfiguration $extensionConfiguration,
    ) {}

    protected function getDescription(): array
    {
        return self::DESCRIPTION;
    }

    protected function getQuestion(): array
    {
        return self::QUESTION;
    }

    public function ask(CommandContext $commandContext, ?string $default = null): mixed
    {
        $extConf = ExtConf::create($this->extensionConfiguration);
        $lastExtension = $default ?? $this->registry->get(ExtConf::EXT_KEY, ExtConf::LAST_EXTENSION_REGISTRY_KEY);
        $availableExtensions = $extConf->getAvailableExtensions();
        $commandContext->getIo()->text($this->getDescription());

        if ($availableExtensions !== []) {
            $extensionKey = $this->askQuestion($this->createSymfonyChoiceQuestion([], $availableExtensions, $default ?? $lastExtension), $commandContext);
            $this->registry->set(ExtConf::EXT_KEY, ExtConf::LAST_EXTENSION_REGISTRY_KEY, $extensionKey);
        } else {
            $commandContext->getIo()->error('No extensions found at path ' . $extConf->getExportDirectory());
            $commandContext->getIo()->info('Create an extension using command make:extension or make:site-package first. ');
            die();
        }

        return $extensionKey;
    }
}
