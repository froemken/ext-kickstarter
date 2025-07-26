<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Validator;

class ActionNameValidator
{
    public function validate(string $name): ValidationResult
    {
        if (preg_match('/^\d/', $name)) {
            return ValidationResult::invalid(
                'Action name should not start with a number.',
                $this->suggestActionName($name)
            );
        }

        if (preg_match('/[^a-zA-Z0-9]/', $name)) {
            return ValidationResult::invalid(
                'Action name contains invalid characters. Use only letters and numbers.',
                $this->suggestActionName($name)
            );
        }

        if (preg_match('/^[A-Z]/', $name)) {
            return ValidationResult::invalid(
                'Action must be written in lowerCamelCase like `showAction`.',
                $this->suggestActionName($name)
            );
        }

        if (!str_ends_with($name, 'Action')) {
            return ValidationResult::invalid(
                'Action name must end with `Action`.',
                $this->suggestActionName($name)
            );
        }

        return ValidationResult::valid();
    }

    private function suggestActionName(string $input): string
    {
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $input);
        $cleaned = lcfirst($cleaned);

        if (str_ends_with(strtolower($cleaned), 'action')) {
            $cleaned = substr($cleaned, 0, -6);
        }

        return $cleaned . 'Action';
    }
}
