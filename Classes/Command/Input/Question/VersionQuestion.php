<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Command\Input\Validator\VersionValidator;

class VersionQuestion extends AbstractQuestion
{
    public const ARGUMENT_NAME = 'version';

    private const QUESTION = [
        'Version',
    ];

    private const DESCRIPTION = [
        'The version is needed to differ between the releases of your extension.',
        'Please use semantic version (https://semver.org/)',
        'Use 0.0.* versions for bugfix releases.',
        'Use 0.*.0 versions, if there are any new features.',
        'Use *.0.0 versions, if something huge has changed like supported TYPO3 version or contained API.',
    ];

    public function __construct(
        VersionValidator $validator
    ) {
        $this->validator = $validator;
    }

    public function getArgumentName(): string
    {
        return self::ARGUMENT_NAME;
    }

    protected function getDescription(): array
    {
        return self::DESCRIPTION;
    }

    protected function getQuestion(): array
    {
        return self::QUESTION;
    }

    protected function getDefault(): ?string
    {
        return '0.0.1';
    }
}
