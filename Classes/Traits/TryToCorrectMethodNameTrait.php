<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Traits;

trait TryToCorrectMethodNameTrait
{
    private function tryToCorrectMethodName(string $className, string $appendix = ''): string
    {
        // Remove invalid chars
        $cleanedMethodName = preg_replace('/[^a-zA-Z0-9]/', '', $className);

        // lower case first char
        $cleanedMethodName = lcfirst($cleanedMethodName);

        if ($appendix !== '') {
            // Remove ending with a wrong case like "coMmaND"
            if (str_ends_with(strtolower($cleanedMethodName), strtolower($appendix))) {
                $appendixLength = mb_strlen($appendix);
                $cleanedMethodName = substr($cleanedMethodName, 0, -$appendixLength);
            }

            // Add appendix
            $cleanedMethodName .= $appendix;
        }

        return $cleanedMethodName;
    }
}
