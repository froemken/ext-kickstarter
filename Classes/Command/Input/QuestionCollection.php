<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input;

use FriendsOfTYPO3\Kickstarter\Command\Input\Question\QuestionInterface;
use FriendsOfTYPO3\Kickstarter\Context\CommandContext;

readonly class QuestionCollection
{
    /**
     * @param iterable<QuestionInterface> $questions
     */
    public function __construct(
        private iterable $questions,
    ) {}

    public function askQuestion(string $argumentName, CommandContext $commandContext, ?string $default = null): mixed
    {
        if ($default === null && $commandContext->getInput()->hasArgument($argumentName)) {
            $default = $commandContext->getInput()->getArgument($argumentName);
        }

        return $this->resolveQuestion($argumentName)->ask($commandContext, $default);
    }

    private function resolveQuestion(string $argumentName): QuestionInterface
    {
        foreach ($this->questions as $question) {
            if ($question->getArgumentName() === $argumentName) {
                return $question;
            }
        }

        throw new \InvalidArgumentException('No question for argument ' . $argumentName . ' found.', 5078287218);
    }
}
