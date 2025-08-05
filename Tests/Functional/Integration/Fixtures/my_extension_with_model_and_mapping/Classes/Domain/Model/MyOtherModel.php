<?php

declare(strict_types=1);

/*
 * This file is part of the package my-vendor/my-extension.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
namespace MyVendor\MyExtension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
final class MyOtherModel extends AbstractEntity
{
    protected string $yyy = '';
    public function getYyy(): string
    {
        return $this->yyy;
    }
    public function setYyy(string $yyy): void
    {
        $this->yyy = $yyy;
    }
}
