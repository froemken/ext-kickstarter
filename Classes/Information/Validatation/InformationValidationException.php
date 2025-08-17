<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validatation;

final class InformationValidationException extends \RuntimeException
{
    public function __construct(private array $errors, int $code = 0)
    {
        parent::__construct('Validation failed', $code);
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
