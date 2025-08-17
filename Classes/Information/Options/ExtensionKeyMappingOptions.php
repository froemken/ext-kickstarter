<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Options;

use FriendsOfTYPO3\Kickstarter\Configuration\ExtConf;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtensionKeyMappingOptions implements OptionsInterface
{
    public function __construct(
        private ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function getOptions(InformationInterface $information): array
    {
        return ExtConf::create($this->extensionConfiguration)->getAvailableExtensions();
    }
}
