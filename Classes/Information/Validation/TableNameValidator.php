<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('ext-kickstarter.information.validator')]
class TableNameValidator implements ValidatorInterface
{
    // tx_ + segments of [a-z0-9] separated by single underscores
    private const CANONICAL_PATTERN = '/^tx_[a-z0-9]+(?:_[a-z0-9]+)*$/';

    public function __invoke(mixed $answer, InformationInterface $information, array $context = []): string
    {
        if (!is_string($answer)) {
            throw new \RuntimeException('Table name must be a string.', 9245302001);
        }

        if ($answer === '') {
            throw new \RuntimeException('Table name cannot be empty.', 9245302002);
        }

        // - lowercase letters & digits only
        // - words separated by a single underscore
        // - no leading/trailing or repeated underscores
        if (in_array(preg_match(self::CANONICAL_PATTERN, $answer), [0, false], true)) {
            throw new \RuntimeException(
                'Table name must start with "tx_" and use only lowercase letters, digits, and single underscores.',
                9245302103
            );
        }

        return $answer;
    }
}
