<?php

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Command\Input\AutoComplete\AutoCompleteInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Decorator\DecoratorInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Normalizer\NormalizerInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Validator\ValidatorInterface;
use StefanFroemken\ExtKickstarter\Context\CommandContext;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

abstract readonly class AbstractQuestion implements QuestionInterface
{
    abstract protected function getDescription(): array;

    abstract protected function getQuestion(): array;

    public function getArgumentName(): string
    {
        return static::ARGUMENT_NAME;
    }

    protected function getDefault(): ?string
    {
        return null;
    }

    protected function createSymfonyQuestion(iterable $inputHandlers, ?string $default = null): Question
    {
        foreach ($inputHandlers as $inputHandler) {
            if ($inputHandler instanceof DecoratorInterface) {
                $default = $inputHandler($default);
                break;
            }
        }

        $symfonyQuestion = new Question(
            implode(' ', $this->getQuestion()),
            $default ?? $this->getDefault(),
        );

        foreach ($inputHandlers as $inputHandler) {
            if ($inputHandler instanceof ValidatorInterface) {
                $symfonyQuestion->setValidator($inputHandler);
                continue;
            }

            if ($inputHandler instanceof NormalizerInterface) {
                $symfonyQuestion->setNormalizer($inputHandler);
                continue;
            }

            if ($inputHandler instanceof AutoCompleteInterface) {
                $symfonyQuestion->setAutocompleterCallback($inputHandler);
            }
        }

        return $symfonyQuestion;
    }

    protected function askQuestion(Question $question, CommandContext $commandContext): mixed
    {
        return (new SymfonyQuestionHelper())->ask(
            $commandContext->getInput(),
            $commandContext->getOutput(),
            $question
        );
    }
}
