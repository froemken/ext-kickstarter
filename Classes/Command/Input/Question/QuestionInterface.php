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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface QuestionInterface
{
    public function getArgumentName(): string;

    public function initializeIO(InputInterface $consoleInput, OutputInterface $consoleOutput): void;

    public function setAutoComplete(?AutoCompleteInterface $autoComplete): void;

    public function setNormalizer(?NormalizerInterface $normalizer): void;

    public function setValidator(?ValidatorInterface $validator): void;

    public function ask(?string $default = null): mixed;
}
