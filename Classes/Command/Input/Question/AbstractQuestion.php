<?php

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Command\Input\AutoComplete\AutoCompleteInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Normalizer\NormalizerInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Validator\ValidatorInterface;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractQuestion implements QuestionInterface
{
    private InputInterface $consoleInput;

    private OutputInterface $consoleOutput;

    private SymfonyStyle $io;

    private ?AutoCompleteInterface $autoComplete;

    private ?NormalizerInterface $normalizer;

    private ?ValidatorInterface $validator;

    public function initializeIO(InputInterface $consoleInput, OutputInterface $consoleOutput): void
    {
        $this->consoleInput = $consoleInput;
        $this->consoleOutput = $consoleOutput;

        $this->io = new SymfonyStyle($consoleInput, $consoleOutput);
    }

    public function setAutoComplete(?AutoCompleteInterface $autoComplete): void
    {
        $this->autoComplete = $autoComplete;
    }

    public function setNormalizer(?NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    public function setValidator(?ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    abstract protected function getDescription(): array;

    abstract protected function getQuestion(): array;

    public function ask(?string $default = null): mixed
    {
        $this->io->text($this->getDescription());

        $question = new Question(implode(' ', $this->getQuestion()), $default);

        if ($this->autoComplete instanceof AutoCompleteInterface) {
            $question->setAutocompleterCallback($this->autoComplete);
        }

        if ($this->normalizer instanceof NormalizerInterface) {
            $question->setNormalizer($this->normalizer);
        }

        if ($this->validator instanceof ValidatorInterface) {
            $question->setValidator($this->validator);
        }

        return (new SymfonyQuestionHelper())->ask($this->consoleInput, $this->consoleOutput, $question);
    }
}
