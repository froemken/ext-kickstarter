<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input;

use StefanFroemken\ExtKickstarter\Command\Input\Question\QuestionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

readonly class QuestionFactory
{
    /**
     * @param iterable<QuestionInterface> $questions
     */
    public function __construct(
        private iterable $questions,
        private AutoCompleteFactory $autoCompleteFactory,
        private NormalizerFactory $normalizerFactory,
        private ValidatorFactory $validatorFactory,
    ) {}

    public function getQuestion(
        string $propertyName,
        InputInterface $consoleInput,
        OutputInterface $consoleOutput
    ): QuestionInterface {
        foreach ($this->questions as $question) {
            if ($question->getArgumentName() === $propertyName) {
                $question->initializeIO($consoleInput, $consoleOutput);
                $question->setAutoComplete($this->autoCompleteFactory->getAutoComplete($propertyName));
                $question->setNormalizer($this->normalizerFactory->getNormalizer($propertyName));
                $question->setValidator($this->validatorFactory->getValidator($propertyName));
                return $question;
            }
        }

        throw new \RuntimeException('Question not found.', 1753826433);
    }
}
