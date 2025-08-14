<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

readonly class ExtTablesSqlInformation
{
    public function __construct(
        private string $columnName,
        private string $columnType,
        private bool $notNull = true, // Default in Doctrine
        private bool $unsigned = false, // Default in Doctrine
        private mixed $default = null, // Default in Doctrine
        private ?int $length = null,
        private ?int $precision = null,
        private ?int $scale = null,
        private CreatorInformation $creatorInformation = new CreatorInformation()
    ) {}

    private function getQuotedColumnName(): string
    {
        return '`' . $this->columnName . '`';
    }

    private function getColumnTypeWithLength(): string
    {
        $columnTypeWithLength = $this->columnType;

        if ($this->length !== null) {
            $columnTypeWithLength .= '(' . $this->length . ')';
        } elseif ($this->precision !== null && $this->scale !== null) {
            $columnTypeWithLength .= '(' . $this->precision . ',' . $this->scale . ')';
        }

        return $columnTypeWithLength;
    }

    /**
     * Returns a string like: "`event_type` varchar(255) DEFAULT '' NOT NULL,"
     */
    public function getColumnDefinitionSql(): string
    {
        // event_type varchar(255) DEFAULT '' NOT NULL,
        $definitionSql = $this->getQuotedColumnName() . ' ' . $this->getColumnTypeWithLength();

        if ($this->unsigned) {
            $definitionSql .= ' UNSIGNED';
        }

        if ($this->default !== null) {
            if (is_string($this->default)) {
                $definitionSql .= ' DEFAULT \'' . $this->default . '\'';
            } else {
                $definitionSql .= ' DEFAULT ' . $this->default;
            }
        }

        if ($this->notNull) {
            $definitionSql .= ' NOT NULL';
        }

        return $definitionSql . ',';
    }

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }
}
