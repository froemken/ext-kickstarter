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
use FriendsOfTYPO3\Kickstarter\Information\TableInformation;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.inputHandler.event-class')]
class TableNameNormalizer implements NormalizerInterface
{
    public function __invoke(?string $userInput, InformationInterface $information): string
    {
        if (!$information instanceof TableInformation) {
            throw new \RuntimeException(self::class . ' only supports ' . TableInformation::class, 1863607137);
        }
        $extensionName = $information->getExtensionInformation()->getExtensionKey();
        $extensionName = strtolower($extensionName);
        // 3) remove ALL non-alphanumerics
        $extensionName = preg_replace('/[^a-z0-9]+/', '', $extensionName);
        if ($userInput === null || $userInput === '') {
            return '';
        }

        $tableName = $userInput;

        // 1) Replace all non-alphanumeric chars with underscores
        $tableName = preg_replace('/[^A-Za-z0-9]+/', '_', $tableName);

        // 2) Handle CamelCase:
        //    - Split UPPERCASE sequences followed by Capital+lowercase: "HTTPServer" -> "HTTP_Server"
        $tableName = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $tableName);
        //    - Split between lowercase/digit and Capital: "userID" -> "user_ID", "aB" -> "a_B"
        $tableName = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', $tableName);

        // 3) Lowercase everything
        $tableName = strtolower($tableName);

        // 4) Collapse multiple underscores and trim leading/trailing ones
        $tableName = preg_replace('/_+/', '_', $tableName);
        $tableName = trim($tableName, '_');

        if (!str_starts_with($tableName, 'tx_')) {
            return 'tx_' . $extensionName . '_' . $tableName;
        }

        return $tableName;
    }
}
