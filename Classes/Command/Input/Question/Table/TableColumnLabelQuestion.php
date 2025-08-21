<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question\Table;

use FriendsOfTYPO3\Kickstarter\Command\Input\Question\AbstractAttributeQuestion;
use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use FriendsOfTYPO3\Kickstarter\Information\TableColumnInformation;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.command.extension.attribute.question')]
readonly class TableColumnLabelQuestion extends AbstractAttributeQuestion
{
    public const ARGUMENT_NAME = 'label';

    public const INFORMATION_CLASS = TableColumnInformation::class;

    public const QUESTION = [
        'Please provide a label for the column',
    ];

    public function ask(CommandContext $commandContext, InformationInterface $information, ?string $default = null): void
    {
        if (!$information instanceof TableColumnInformation) {
            throw new \InvalidArgumentException(sprintf(
                'Expected %s, got %s',
                self::INFORMATION_CLASS,
                get_debug_type($information)
            ), 9703445163);
        }

        $information->setLabel($this->askQuestion(
            $this->createSymfonyQuestion($information, $default),
            $commandContext
        ));
    }
}
