<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

final class InformationValidationException extends \RuntimeException
{
    public function __construct(private array $errors, int $code = 0)
    {
        parent::__construct('Validation failed: ' . implode(', ', $errors), $code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
