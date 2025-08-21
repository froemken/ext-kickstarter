<?php

namespace FriendsOfTYPO3\Kickstarter\Information\DefaultValue;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\TableNameNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\TableInformation;

class TableNameDefaultValue implements DefaultValueInterface
{
    public function __construct(
        private readonly TableNameNormalizer $normalizer
    ) {}

    public function getDefaultValue(InformationInterface $information): ?string
    {
        if (!$information instanceof TableInformation) {
            return null;
        }
        return $this->normalizer->__invoke($information->getTitle(), $information);
    }
}
