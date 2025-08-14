<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Validator;

class ClassNameValidator implements ValidatorInterface
{
    public function __invoke(mixed $answer): string
    {
        if ($answer === null || $answer === '') {
            throw new \RuntimeException('Class name must not be empty.', 7856569272);
        }
        if (preg_match('/^\d/', $answer)) {
            throw new \RuntimeException('Class name must not start with a number.', 8716512611);
        }
        if (preg_match('/[^a-zA-Z0-9]/', $answer)) {
            throw new \RuntimeException('Class name contains invalid chars. Please provide just letters and numbers.', 9569056953);
        }
        if (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $answer) === 0) {
            throw new \RuntimeException('Class name must be written in UpperCamelCase like "ProcessRequestEvent".', 8916750461);
        }

        return $answer;
    }
}
