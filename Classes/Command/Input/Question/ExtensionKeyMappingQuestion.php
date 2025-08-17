<?php

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question;

use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionMappingInformation;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionRelatedInformationInterface;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.command.extension.attribute.question')]
readonly class ExtensionKeyMappingQuestion extends AbstractAttributeQuestion
{
    public const ARGUMENT_NAME = 'extensionInformation';

    public const INFORMATION_CLASS = ExtensionRelatedInformationInterface::class;

    private const QUESTION = [
        'Please provide the class name of your new Event',
    ];

    public function __construct() {}

    protected function getQuestion(): array
    {
        return self::QUESTION;
    }

    public function ask(CommandContext $commandContext, InformationInterface $information, ?string $default = null): void
    {
        if (!$information instanceof ExtensionRelatedInformationInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Expected %s, got %s',
                self::INFORMATION_CLASS,
                get_debug_type($information)
            ), 6481694059);
        }
        $extensionMappingInformation = new ExtensionMappingInformation();
        $extensionMappingInformation->setExtensionKey(
            $this->askQuestion(
                $this->createSymfonyChoiceQuestion($extensionMappingInformation, $default),
                $commandContext
            )
        );
        $information->setExtensionInformation($extensionMappingInformation);
    }
}
