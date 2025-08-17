<?php

namespace FriendsOfTYPO3\Kickstarter\Information\DefaultValue;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;

interface DefaultValueInterface
{
    public function getDefaultValue(InformationInterface $information): ?string;
}
