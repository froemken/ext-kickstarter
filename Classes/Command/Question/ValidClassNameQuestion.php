<?php

namespace StefanFroemken\ExtKickstarter\Command\Question;

use StefanFroemken\ExtKickstarter\Validator\ClassNameValidator;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class ValidClassNameQuestion
{
    public function __construct(private ClassNameValidator $classNameValidator) {}

    public function ask(
        SymfonyStyle $io,
        string $prompt,
        string $contextName,
        string $expectedSuffix = '',
    ): string {
        $default = null;

        do {
            $input = (string)$io->ask($prompt, $default);
            $result = $this->classNameValidator->validate($input, $contextName, $expectedSuffix);

            if (!$result->isValid) {
                $io->error($result->error);
                $default = $result->suggestion;
            }
        } while (!$result->isValid);

        return $input;
    }
}
