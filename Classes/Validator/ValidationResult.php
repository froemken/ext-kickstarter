<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Validator;

final class ValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly ?string $error = null,
        public readonly ?string $suggestion = null,
    ) {}

    public static function valid(): self
    {
        return new self(true);
    }

    public static function invalid(string $error, ?string $suggestion = null): self
    {
        return new self(false, $error, $suggestion);
    }
}
