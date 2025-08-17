<?php

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question;

use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\DefaultValueInterface;
use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\ProvideDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\UseNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Options\OptionsInterface;
use FriendsOfTYPO3\Kickstarter\Information\Options\ProvideOptions;
use FriendsOfTYPO3\Kickstarter\Information\Validatation\UseValidator;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract readonly class AbstractAttributeQuestion implements AttributeQuestionInterface
{
    public function getDefaultValueGenerator(ProvideDefaultValue $meta): DefaultValueInterface
    {
        return GeneralUtility::makeInstance($meta->serviceId);
    }

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

    protected function getDefault(InformationInterface $information): ?string
    {
        $rc = new \ReflectionClass($information);
        foreach ($rc->getProperties() as $prop) {
            $defaultValueAttribute = $prop->getAttributes(ProvideDefaultValue::class)[0] ?? null;
            if ($defaultValueAttribute !== null) {
                /** @var ProvideDefaultValue $meta */
                $meta = $defaultValueAttribute->newInstance();
                $defaultValueGenerator = $this->getDefaultValueGenerator($meta);
                return $defaultValueGenerator->getDefaultValue($information);
            }
        }
        return null;
    }

    protected function createSymfonyQuestion(InformationInterface $information, ?string $default = null): Question
    {
        $symfonyQuestion = new Question(
            implode(' ', $this->getQuestion()),
            $default ?? $this->getDefault($information),
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

    protected function createSymfonyChoiceQuestion(InformationInterface $information, ?string $default = null): ChoiceQuestion
    {
        $rc = new \ReflectionClass($information);
        $choices = [];
        foreach ($rc->getProperties() as $prop) {
            $optionsAttribute = $prop->getAttributes(ProvideOptions::class)[0] ?? null;
            if ($optionsAttribute !== null) {
                /** @var UseNormalizer $meta */
                $meta = $optionsAttribute->newInstance();
                /** @var OptionsInterface $optionsGenerator */
                $optionsGenerator = GeneralUtility::makeInstance($meta->serviceId);
                $choices = $optionsGenerator->getOptions($information);
            }
        }

        return new ChoiceQuestion(
            implode(' ', $this->getQuestion()),
            $choices,
            $default ?? $this->getDefault($information),
        );
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
