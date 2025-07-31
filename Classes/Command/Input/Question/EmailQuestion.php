<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

class EmailQuestion extends AbstractQuestion
{
    public const ARGUMENT_NAME = 'email';

    private const QUESTION = [
        'Email address',
    ];

    private const DESCRIPTION = [
        'Please enter the email of the author (see above)',
        'It must be a valid email address.',
    ];

    public function getArgumentName(): string
    {
        return self::ARGUMENT_NAME;
    }

    protected function getDescription(): array
    {
        return self::DESCRIPTION;
    }

    protected function getQuestion(): array
    {
        return self::QUESTION;
    }
}
