<?php

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question;

use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionMappingInformation;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.command.extension.attribute.question')]
readonly class ExtensionKeyMappingQuestion extends AbstractAttributeQuestion
{
    public const ARGUMENT_NAME = 'extensionKey';

    public const INFORMATION_CLASS = ExtensionMappingInformation::class;

    public const QUESTION = [
        'Choose an existing extension',
    ];

    public function ask(CommandContext $commandContext, InformationInterface $information, ?string $default = null): void
    {
        if (!$information instanceof ExtensionMappingInformation) {
            throw new \InvalidArgumentException(sprintf(
                'Expected %s, got %s',
                self::INFORMATION_CLASS,
                get_debug_type($information)
            ), 6481694059);
        }

        $information->setExtensionKey(
            $this->askQuestion(
                $this->createSymfonyChoiceQuestion($information, $default),
                $commandContext
            )
        );
    }
}
