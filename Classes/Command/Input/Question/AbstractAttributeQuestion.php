<?php

namespace FriendsOfTYPO3\Kickstarter\Command\Input\Question;

use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\DefaultValueInterface;
use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\ProvideDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\NormalizerInterface;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\UseNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Options\OptionsInterface;
use FriendsOfTYPO3\Kickstarter\Information\Options\ProvideOptions;
use FriendsOfTYPO3\Kickstarter\Information\Validation\UseValidator;
use FriendsOfTYPO3\Kickstarter\Information\Validation\ValidatorInterface;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract readonly class AbstractAttributeQuestion implements AttributeQuestionInterface
{
    protected function getQuestion(): array
    {
        $calledClass = static::class;

        if (!defined($calledClass . '::QUESTION')) {
            throw new \RuntimeException(
                sprintf('Class %s must define the constant QUESTION.', $calledClass),
                5808821319,
            );
        }

        return $calledClass::QUESTION;
    }

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
        $prop = $this->getReflectionProperty($information);
        $defaultValueAttribute = $prop->getAttributes(ProvideDefaultValue::class)[0] ?? null;
        if ($defaultValueAttribute !== null) {
            /** @var ProvideDefaultValue $meta */
            $meta = $defaultValueAttribute->newInstance();
            $defaultValueGenerator = $this->getDefaultValueGenerator($meta);
            return $defaultValueGenerator->getDefaultValue($information);
        }
        return null;
    }

    protected function createSymfonyQuestion(InformationInterface $information, ?string $default = null): Question
    {
        $symfonyQuestion = new Question(
            implode(' ', $this->getQuestion()),
            $default ?? $this->getDefault($information),
        );

        $prop = $this->getReflectionProperty($information);
        $normAttr = $prop->getAttributes(UseNormalizer::class)[0] ?? null;
        if ($normAttr !== null) {
            /** @var UseNormalizer $meta */
            $meta = $normAttr->newInstance();
            $normalizer = $this->getNormalizer($meta);
            $symfonyQuestion->setNormalizer(
                static fn(?string $value): string => $normalizer($value, $information)
            );
        }
        $valAttr = $prop->getAttributes(UseValidator::class)[0] ?? null;
        if ($valAttr !== null) {
            /** @var UseValidator $meta */
            $meta = $valAttr->newInstance();
            $validator = $this->getValidator($meta);
            $symfonyQuestion->setValidator(
                static fn(mixed $answer): string => $validator($answer, $information)
            );
        }

        return $symfonyQuestion;
    }

    protected function createSymfonyChoiceQuestion(InformationInterface $information, ?string $default = null): ChoiceQuestion
    {
        new \ReflectionClass($information);
        $choices = [];
        $prop = $this->getReflectionProperty($information);
        $optionsAttribute = $prop->getAttributes(ProvideOptions::class)[0] ?? null;
        if ($optionsAttribute !== null) {
            /** @var ProvideOptions $meta */
            $meta = $optionsAttribute->newInstance();
            $optionsGenerator = $this->getOptionsGenerator($meta);
            $choices = $optionsGenerator->getOptions($information);
        }

        return new ChoiceQuestion(
            implode(' ', $this->getQuestion()),
            $choices,
            $default ?? $this->getDefault($information) ?? 0,
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

    private function getNormalizer(UseNormalizer $meta): NormalizerInterface
    {
        return GeneralUtility::makeInstance($meta->serviceId);
    }

    private function getValidator(UseValidator $meta): ValidatorInterface
    {
        return GeneralUtility::makeInstance($meta->serviceId);
    }

    private function getOptionsGenerator(ProvideOptions $meta): OptionsInterface
    {
        return GeneralUtility::makeInstance($meta->serviceId);
    }

    private function getDefaultValueGenerator(ProvideDefaultValue $meta): DefaultValueInterface
    {
        return GeneralUtility::makeInstance($meta->serviceId);
    }

    private function getReflectionProperty(InformationInterface $information): \ReflectionProperty
    {
        $rc = new \ReflectionClass($information);

        if (!$rc->hasProperty($this->getArgumentName())) {
            throw new \Exception(sprintf('Information %s does not have property $%s as required by question %s', $information::class, $this->getArgumentName(), $this::class), 1287154076);
        }
        return $rc->getProperty($this->getArgumentName());
    }
}
