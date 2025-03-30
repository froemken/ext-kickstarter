<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

class TableInformation
{
    private const TABLE_PATH = 'Configuration/TCA/';

    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
        private readonly string $tableName,
        private readonly string $title,
        private readonly string $label,
        private readonly array $columns,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getTableConfigurationPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::TABLE_PATH;
    }

    public function getFullTableConfigurationFilePath(): string
    {
        return $this->getTableConfigurationPath() . $this->getTableName() . '.php';
    }
}
