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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
final class MyOtherModel extends AbstractEntity
{
    protected ObjectStorage $yyy;
    public function getYyy(): ObjectStorage
    {
        return $this->yyy;
    }
    public function setYyy(ObjectStorage $yyy): void
    {
        $this->yyy = $yyy;
    }
    public function __construct()
    {
        $this->initializeObject();
    }
    public function initializeObject(): void
    {
    }
}
