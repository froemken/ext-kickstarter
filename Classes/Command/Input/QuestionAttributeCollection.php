<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command\Input;

use FriendsOfTYPO3\Kickstarter\Command\Input\Question\AbstractAttributeQuestion;
use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;

readonly class QuestionAttributeCollection
{
    /**
     * @param iterable<AbstractAttributeQuestion> $questions
     */
    public function __construct(
        private iterable $questions,
    ) {}

    public function askQuestion(InformationInterface $information, string $argumentName, CommandContext $commandContext, ?string $default = null): void
    {
        if ($default === null && $commandContext->getInput()->hasArgument($argumentName)) {
            $default = $commandContext->getInput()->getArgument($argumentName);
        }

        $this->resolveQuestion($information, $argumentName)->ask($commandContext, $information, $default);
    }

    private function resolveQuestion(InformationInterface $information, string $argumentName): AbstractAttributeQuestion
    {
        foreach ($this->questions as $question) {
            if (\is_a($information, $question->getInformationClass()) && $question->getArgumentName() === $argumentName) {
                return $question;
            }
        }

        throw new \InvalidArgumentException('No question for information type ' . $information::class . ' argument ' . $argumentName . ' found.', 5078287218);
    }
}
