<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Traits;

trait TryToCorrectClassNameTrait
{
    private function tryToCorrectClassName(string $className, string $appendix = ''): string
    {
        // Remove invalid chars
        $cleanedCommandClassName = preg_replace('/[^a-zA-Z0-9]/', '', $className);

        // Upper case first char
        $cleanedCommandClassName = ucfirst($cleanedCommandClassName);

        if ($appendix !== '') {
            // Remove ending with a wrong case like "coMmaND"
            if (str_ends_with(strtolower($cleanedCommandClassName), strtolower($appendix))) {
                $appendixLength = mb_strlen($appendix);
                $cleanedCommandClassName = substr($cleanedCommandClassName, 0, -$appendixLength);
            }

            // Add appendix
            $cleanedCommandClassName .= $appendix;
        }

        return $cleanedCommandClassName;
    }
}
