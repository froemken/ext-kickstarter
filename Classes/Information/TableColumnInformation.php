<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\Kickstarter\Information;

use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\ProvideDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\TableColumnNameDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\TableColumnNameNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\UseNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Options\ProvideOptions;
use FriendsOfTYPO3\Kickstarter\Information\Options\TcaTypeOptions;
use FriendsOfTYPO3\Kickstarter\Information\Validation\NotEmptyValidator;
use FriendsOfTYPO3\Kickstarter\Information\Validation\TableColumnNameValidator;
use FriendsOfTYPO3\Kickstarter\Information\Validation\UseValidator;

class TableColumnInformation implements InformationInterface
{
    private ?TableInformation $tableInformation = null;

    #[ProvideDefaultValue(TableColumnNameDefaultValue::class)]
    #[UseValidator(TableColumnNameValidator::class)]
    #[UseNormalizer(TableColumnNameNormalizer::class)]
    private ?string $columnName = null;

    #[UseValidator(NotEmptyValidator::class)]
    private ?string $label = null;

    #[ProvideOptions(TcaTypeOptions::class)]
    private ?string $type = null;

    public function getTcaConfig(): array
    {
        return [
            'type' => $this->getType(),
        ];
    }

    public function getTableInformation(): ?TableInformation
    {
        return $this->tableInformation;
    }

    public function setTableInformation(?TableInformation $tableInformation): void
    {
        $this->tableInformation = $tableInformation;
    }

    public function getColumnName(): ?string
    {
        return $this->columnName;
    }

    public function setColumnName(?string $columnName): void
    {
        $this->columnName = $columnName;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
