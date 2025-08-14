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

readonly class QuestionFactory
{
    /**
     * @param iterable<QuestionInterface> $questions
     */
    public function __construct(
        private iterable $questions,
    ) {}

    public function getQuestion(
        string $propertyName
    ): QuestionInterface {
        foreach ($this->questions as $question) {
            if ($question->getArgumentName() === $propertyName) {
                return $question;
            }
        }

        throw new \RuntimeException(sprintf('Question for property %s not found.', $propertyName), 1753826433);
    }
}
