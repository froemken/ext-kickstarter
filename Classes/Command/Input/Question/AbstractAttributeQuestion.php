<?php

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question;

use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\UseNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Validatation\UseValidator;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract readonly class AbstractAttributeQuestion implements AttributeQuestionInterface
{
    abstract protected function getQuestion(): array;

    public function getArgumentName(): string
    {
        $calledClass = static::class;

        if (!defined($calledClass . '::ARGUMENT_NAME')) {
            throw new \RuntimeException(
                sprintf('Class %s must define the constant ARGUMENT_NAME.', $calledClass),
                3362475211
            );
        }

        return $calledClass::ARGUMENT_NAME;
    }

    public function getInformationClass(): string
    {
        $calledClass = static::class;

        if (!defined($calledClass . '::INFORMATION_CLASS')) {
            throw new \RuntimeException(
                sprintf('Class %s must define the constant INFORMATION_CLASS.', $calledClass),
                9394169090,
            );
        }

        return $calledClass::INFORMATION_CLASS;
    }

    protected function getDefault(): ?string
    {
        return null;
    }

    protected function createSymfonyQuestion(InformationInterface $information, ?string $default = null): Question
    {
        $symfonyQuestion = new Question(
            implode(' ', $this->getQuestion()),
            $default ?? $this->getDefault(),
        );

        $rc = new \ReflectionClass($information);

        foreach ($rc->getProperties() as $prop) {
            $valAttr = $prop->getAttributes(UseValidator::class)[0] ?? null;
            $normAttr = $prop->getAttributes(UseNormalizer::class)[0] ?? null;
            if ($normAttr !== null) {
                /** @var UseNormalizer $meta */
                $meta = $normAttr->newInstance();
                $normalizer = GeneralUtility::makeInstance($meta->serviceId);
                $symfonyQuestion->setNormalizer($normalizer);
            }
            if ($valAttr !== null) {
                /** @var UseValidator $meta */
                $meta = $valAttr->newInstance();
                $validator = GeneralUtility::makeInstance($meta->serviceId);
                $symfonyQuestion->setValidator($validator);
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
