<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\ProvideDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\TableNameDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\TableNameNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\UseNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Validation\NotEmptyValidator;
use FriendsOfTYPO3\Kickstarter\Information\Validation\TableNameValidator;
use FriendsOfTYPO3\Kickstarter\Information\Validation\UseValidator;

class TableInformation implements ExtensionRelatedInformationInterface
{
    private const TABLE_PATH = 'Configuration/TCA/';

    private ?ExtensionInformation $extensionInformation = null;

    #[ProvideDefaultValue(TableNameDefaultValue::class)]
    #[UseValidator(TableNameValidator::class)]
    #[UseNormalizer(TableNameNormalizer::class)]
    private ?string $tableName = null;

    #[UseValidator(NotEmptyValidator::class)]
    private ?string $title = null;

    private ?string $label = null;

    /** @var array<TableColumnInformation>|null  */
    private ?array $columns = null;

    private CreatorInformation $creatorInformation;

    public function __construct(
        ?ExtensionInformation $extensionInformation = null,
        ?string $tableName = null,
        ?string $title = null,
        ?string $label = null,
        ?array $columns = null,
        ?CreatorInformation $creatorInformation = null
    ) {
        $this->extensionInformation = $extensionInformation;
        $this->tableName = $tableName;
        $this->title = $title;
        $this->label = $label;
        $this->columns = $columns;
        $this->creatorInformation = $creatorInformation ?? new CreatorInformation();
    }

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return array<TableColumnInformation>|null
     */
    public function getColumns(): ?array
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

    public function getCreatorInformation(): ?CreatorInformation
    {
        return $this->creatorInformation;
    }

    public function setExtensionInformation(ExtensionInformation $extensionInformation): void
    {
        $this->extensionInformation = $extensionInformation;
    }

    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param array<TableColumnInformation> $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    public function setCreatorInformation(CreatorInformation $creatorInformation): void
    {
        $this->creatorInformation = $creatorInformation;
    }
}
