<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.information.validator')]
class TableColumnNameValidator implements ValidatorInterface
{
    private const CANONICAL_PATTERN = '/^[a-z][a-z0-9]*(?:_[a-z0-9]+)*$/';

    public function __invoke(mixed $answer, InformationInterface $information, array $context = []): string
    {
        if (!is_string($answer)) {
            throw new \RuntimeException('Table column name must be a string.', 3509305797);
        }

        if ($answer === '') {
            throw new \RuntimeException('Table column name cannot be empty.', 1297582111);
        }

        // - lowercase letters & digits only
        // - words separated by a single underscore
        // - no leading/trailing or repeated underscores
        if (in_array(preg_match(self::CANONICAL_PATTERN, $answer), [0, false], true)) {
            throw new \RuntimeException(
                'Table column name may use only lowercase letters, digits, and single underscores.',
                9745320221,
            );
        }

        return $answer;
    }
}
