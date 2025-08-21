<?php

namespace FriendsOfTYPO3\Kickstarter\Information\DefaultValue;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\TableColumnNameNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\TableColumnInformation;

class TableColumnNameDefaultValue implements DefaultValueInterface
{
    public function __construct(
        private readonly TableColumnNameNormalizer $normalizer
    ) {}

    public function getDefaultValue(InformationInterface $information): ?string
    {
        if (!$information instanceof TableColumnInformation) {
            return null;
        }
        return $this->normalizer->__invoke($information->getLabel(), $information);
    }
}
