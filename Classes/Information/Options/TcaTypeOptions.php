<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Options;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use FriendsOfTYPO3\Kickstarter\Service\Creator\TableCreatorService;

class TcaTypeOptions implements OptionsInterface
{
    public function getOptions(InformationInterface $information): array
    {
        return array_keys(TableCreatorService::TABLE_COLUMN_TYPES);
    }
}
