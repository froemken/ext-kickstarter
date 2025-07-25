<?php

namespace StefanFroemken\ExtKickstarter\Traits;

use StefanFroemken\ExtKickstarter\Validator\ClassNameValidator;
use Symfony\Component\Console\Style\SymfonyStyle;

trait AskForValidClassNameTrait
{
    private ClassNameValidator $classNameValidator;

    public function setClassNameValidator(ClassNameValidator $validator): void
    {
        $this->classNameValidator = $validator;
    }

    protected function askForValidClassName(
        SymfonyStyle $io,
        string $prompt,
        string $contextName,
        string $expectedSuffix = ''
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
