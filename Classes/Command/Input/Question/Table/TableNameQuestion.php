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
use FriendsOfTYPO3\Kickstarter\Information\TableInformation;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.command.extension.attribute.question')]
readonly class TableNameQuestion extends AbstractAttributeQuestion
{
    public const ARGUMENT_NAME = 'tableName';

    public const INFORMATION_CLASS = TableInformation::class;

    public const QUESTION = [
        'Name of the new table',
    ];

    public function ask(CommandContext $commandContext, InformationInterface $information, ?string $default = null): void
    {
        if (!$information instanceof TableInformation) {
            throw new \InvalidArgumentException(sprintf(
                'Expected %s, got %s',
                self::INFORMATION_CLASS,
                get_debug_type($information)
            ), 7985421564);
        }
        $prefix = 'tx_myextension_'; //$information->getExtensionInformation()->getTableNamePrefix();
        $commandContext->getIo()->text('Please provide the table name. Usually the table name starts with: ' . $prefix);

        $information->setTableName($this->askQuestion(
            $this->createSymfonyQuestion($information, $default),
            $commandContext
        ));
    }
}
