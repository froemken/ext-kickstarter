<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information\Normalization;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AutoconfigureTag('ext-kickstarter.inputHandler.event-class')]
class TableColumnNameNormalizer implements NormalizerInterface
{
    public function __invoke(?string $userInput, InformationInterface $information): string
    {
        // Change dash to underscore
        $cleanedColumnName = str_replace('-', '_', $userInput ?? '');

        // Change column name to lower camel case. Add underscores before upper case letters. BlogExample => blog_example
        $cleanedColumnName = GeneralUtility::camelCaseToLowerCaseUnderscored($cleanedColumnName);

        // Remove invalid chars
        return preg_replace('/[^a-zA-Z0-9_]/', '', $cleanedColumnName);
    }
}
