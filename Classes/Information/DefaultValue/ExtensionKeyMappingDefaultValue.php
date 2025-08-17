<?php

namespace FriendsOfTYPO3\Kickstarter\Information\DefaultValue;

use FriendsOfTYPO3\Kickstarter\Configuration\ExtConf;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use TYPO3\CMS\Core\Registry;

class ExtensionKeyMappingDefaultValue implements DefaultValueInterface
{
    public function __construct(
        private Registry $registry,
    ) {}

    public function getDefaultValue(InformationInterface $information): ?string
    {
        $lastExtension = $this->registry->get(ExtConf::EXT_KEY, ExtConf::LAST_EXTENSION_REGISTRY_KEY);
        if (!is_string($lastExtension)) {
            return null;
        }
        return $lastExtension;
    }
}
