<?php

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Command\Input\AutoComplete\AutoCompleteInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Normalizer\NormalizerInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Validator\ValidatorInterface;
use StefanFroemken\ExtKickstarter\Context\CommandContext;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

abstract class AbstractQuestion implements QuestionInterface
{
    protected ?AutoCompleteInterface $autoComplete = null;

    protected ?NormalizerInterface $normalizer = null;

    protected ?ValidatorInterface $validator = null;

    abstract protected function getDescription(): array;

    abstract protected function getQuestion(): array;

    protected function getDefault(): ?string
    {
        return null;
    }

    public function ask(CommandContext $commandContext, ?string $default = null): mixed
    {
        $commandContext->getIo()->text($this->getDescription());

        $question = new Question(implode(' ', $this->getQuestion()), $default ?? $this->getDefault());

        if ($this->autoComplete instanceof AutoCompleteInterface) {
            $question->setAutocompleterCallback($this->autoComplete);
        }

        if ($this->normalizer instanceof NormalizerInterface) {
            $question->setNormalizer($this->normalizer);
        }

        if ($this->validator instanceof ValidatorInterface) {
            $question->setValidator($this->validator);
        }

        return (new SymfonyQuestionHelper())->ask($commandContext->getInput(), $commandContext->getOutput(), $question);
    }
}
