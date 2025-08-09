<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input\Question;

use StefanFroemken\ExtKickstarter\Context\CommandContext;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.command.extension.question')]
readonly class PluginNameQuestion extends AbstractQuestion
{
    public const ARGUMENT_NAME = 'plugin-name';

    private const QUESTION = [
        'Please provide the name of your plugin. This is an internal identifier and will be used to reference your plugin in the backend',
    ];

    private const DESCRIPTION = [];

    public function __construct(
        private iterable $inputHandlers,
    ) {}

    protected function getDescription(): array
    {
        return self::DESCRIPTION;
    }

    protected function getQuestion(): array
    {
        return self::QUESTION;
    }

    public function ask(CommandContext $commandContext, ?string $default = null): mixed
    {
        $commandContext->getIo()->text($this->getDescription());

        return $this->askQuestion(
            $this->createSymfonyQuestion($this->inputHandlers, $default),
            $commandContext
        );
    }
}
