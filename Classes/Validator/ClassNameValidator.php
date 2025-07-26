<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Validator;

readonly class ClassNameValidator
{
    public function validate(string $name, string $contextName, ?string $requiredSuffix = null): ValidationResult
    {
        if (preg_match('/^\d/', $name)) {
            return ValidationResult::invalid(
                sprintf('%s name should not start with a number.', $contextName),
                $this->suggestName($name, $requiredSuffix)
            );
        }

        if (preg_match('/[^a-zA-Z0-9]/', $name)) {
            return ValidationResult::invalid(
                sprintf('%s name contains invalid characters. Use only letters and numbers.', $contextName),
                $this->suggestName($name, $requiredSuffix)
            );
        }

        if (preg_match('/^[A-Z][a-zA-Z0-9]*$/', $name) === 0) {
            return ValidationResult::invalid(
                sprintf("$contextName name must be in UpperCamelCase, e.g., `My%s`.", $requiredSuffix ?? ''),
                $this->suggestName($name, $requiredSuffix)
            );
        }

        if ($requiredSuffix && !str_ends_with($name, $requiredSuffix)) {
            return ValidationResult::invalid(
                sprintf('%s name must end with `%s`.', $contextName, $requiredSuffix),
                $this->suggestName($name, $requiredSuffix)
            );
        }

        if ($requiredSuffix && strtolower($name) === strtolower($requiredSuffix)) {
            return ValidationResult::invalid(
                sprintf('%s name cannot be just "%s". Please include a meaningful prefix, e.g., `My%s`.', $contextName, $requiredSuffix, $requiredSuffix),
                $this->suggestName($name, $requiredSuffix)
            );
        }

        return ValidationResult::valid();
    }

    private function suggestName(string $input, ?string $suffix = null): string
    {
        // Remove invalid characters
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $input);
        $cleaned = ucfirst($cleaned);

        if ($suffix !== null && $suffix !== '' && $suffix !== '0') {
            // Remove incorrect-case suffix if present
            if (str_ends_with(strtolower($cleaned), strtolower($suffix))) {
                $cleaned = substr($cleaned, 0, -strlen($suffix));
            }
            if ($cleaned === '') {
                $cleaned = 'My';
            }

            // Append proper-case suffix
            $cleaned .= $suffix;
        }

        return $cleaned;
    }
}
