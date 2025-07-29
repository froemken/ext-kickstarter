<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Command\Input\AutoComplete\AutoCompleteInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Normalizer\NormalizerInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Validator\ValidatorInterface;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExtensionKeyQuestion implements QuestionInterface
{
    private const ARGUMENT_NAME = 'extension_key';

    private const QUESTION = [
        'Please provide the key for your extension',
    ];

    private const DESCRIPTION = [
        'Building a new TYPO3 extension needs a unique identifier, the so called extension key. See:',
        'https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/BestPractises/ExtensionKey.html',
    ];

    private InputInterface $consoleInput;

    private OutputInterface $consoleOutput;

    private SymfonyStyle $io;

    private ?AutoCompleteInterface $autoComplete;

    private ?NormalizerInterface $normalizer;

    private ?ValidatorInterface $validator;

    public function getArgumentName(): string
    {
        return self::ARGUMENT_NAME;
    }

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

    public function ask(?string $default = null): mixed
    {
        $this->io->text(self::DESCRIPTION);

        $question = new Question(implode(' ', self::QUESTION), $default);

        if ($this->autoComplete instanceof AutoCompleteInterface) {
            $question->setValidator($this->autoComplete);
        }

        if ($this->validator instanceof NormalizerInterface) {
            $question->setValidator($this->normalizer);
        }

        if ($this->validator instanceof ValidatorInterface) {
            $question->setValidator($this->validator);
        }

        return (new SymfonyQuestionHelper())->ask($this->consoleInput, $this->consoleOutput, $question);
    }
}
