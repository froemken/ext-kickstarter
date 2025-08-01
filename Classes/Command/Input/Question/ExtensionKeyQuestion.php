<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Command\Input\AutoComplete\ExtensionKeyAutoComplete;
use StefanFroemken\ExtKickstarter\Command\Input\Normalizer\ExtensionKeyNormalizer;
use StefanFroemken\ExtKickstarter\Command\Input\Validator\ExtensionKeyValidator;

class ExtensionKeyQuestion extends AbstractQuestion
{
    private const ARGUMENT_NAME = 'extension_key';

    private const QUESTION = [
        'Please provide the key for your extension',
    ];

    private const DESCRIPTION = [
        'Building a new TYPO3 extension needs a unique identifier, the so called extension key. See:',
        'https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ExtensionArchitecture/BestPractises/ExtensionKey.html',
    ];

    public function __construct(
        ExtensionKeyValidator $validator,
        ExtensionKeyNormalizer $normalizer,
        ExtensionKeyAutoComplete $autoComplete,
    ) {
        $this->validator = $validator;
        $this->normalizer = $normalizer;
        $this->autoComplete = $autoComplete;
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
}
