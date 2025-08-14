<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

class ExtTablesSqlInformation
{
    public function __construct(
        private readonly string $columnName,
        private readonly string $columnType,
        private readonly bool $notNull = true, // Default in Doctrine
        private readonly bool $unsigned = false, // Default in Doctrine
        private readonly mixed $default = null, // Default in Doctrine
        private readonly ?int $length = null,
        private readonly ?int $precision = null,
        private readonly ?int $scale = null,
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
}
