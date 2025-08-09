<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Validator;

class MethodNameValidator implements ValidatorInterface
{
    public function __invoke(mixed $answer): string
    {
        if ($answer === null || $answer === '') {
            throw new \RuntimeException('Method name must not be empty.', 3755777377);
        }
        if (preg_match('/^\d/', $answer)) {
            throw new \RuntimeException('Method name should not start with a number.', 2450975096);
        }
        if (preg_match('/[^a-zA-Z0-9]/', $answer)) {
            throw new \RuntimeException('Method name contains invalid chars. Please provide just letters and numbers.', 1022763272);
        }
        if (preg_match('/^[a-z][a-zA-Z0-9]+$/', $answer) === 0) {
            throw new \RuntimeException('Method name must be written in LowerCamelCase like "showAction".', 8916750461);
        }

        return $answer;
    }
}
