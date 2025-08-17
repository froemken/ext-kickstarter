<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Options;

use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;

interface OptionsInterface
{
    public function getOptions(InformationInterface $information): array;
}
