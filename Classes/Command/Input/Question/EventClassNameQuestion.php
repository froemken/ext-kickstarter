<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question;

use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\EventInformation;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.command.extension.attribute.question')]
readonly class EventClassNameQuestion extends AbstractAttributeQuestion
{
    public const ARGUMENT_NAME = 'eventClassName';

    public const INFORMATION_CLASS = EventInformation::class;

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
        if (!$information instanceof EventInformation) {
            throw new \InvalidArgumentException(sprintf(
                'Expected %s, got %s',
                self::INFORMATION_CLASS,
                get_debug_type($information)
            ), 6481694059);
        }
        $information->setEventClassName($this->askQuestion(
            $this->createSymfonyQuestion($information, $default),
            $commandContext
        ));
    }
}
