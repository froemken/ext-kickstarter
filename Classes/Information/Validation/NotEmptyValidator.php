<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;

class NotEmptyValidator implements ValidatorInterface
{
    public function __invoke(mixed $answer, InformationInterface $information, array $context = []): string
    {
        if ($answer === null || $answer === '') {
            $fieldName = $context['fieldName'] ?? null;
            if ($fieldName !== null) {
                throw new \RuntimeException('Field ' . $fieldName . ' in ' . $information::class . ' must not be empty. ', 8095989439);
            }
            throw new \RuntimeException('Must not be empty. ', 7220502468);
        }

        return $answer;
    }
}
