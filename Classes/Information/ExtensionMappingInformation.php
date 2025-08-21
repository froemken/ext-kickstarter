<?php

namespace FriendsOfTYPO3\Kickstarter\Information;

use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\ExtensionKeyMappingDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\DefaultValue\ProvideDefaultValue;
use FriendsOfTYPO3\Kickstarter\Information\Options\ExtensionKeyMappingOptions;
use FriendsOfTYPO3\Kickstarter\Information\Options\ProvideOptions;
use FriendsOfTYPO3\Kickstarter\Information\Validation\ExtensionMappingValidator;
use FriendsOfTYPO3\Kickstarter\Information\Validation\UseValidator;

class ExtensionMappingInformation implements InformationInterface
{
    #[UseValidator(ExtensionMappingValidator::class)]
    #[ProvideDefaultValue(ExtensionKeyMappingDefaultValue::class)]
    #[ProvideOptions(ExtensionKeyMappingOptions::class)]
    private ?string $extensionKey = null;

    public function __construct(?string $extensionKey = null)
    {
        $this->extensionKey = $extensionKey;
    }

    public function getExtensionKey(): ?string
    {
        return $this->extensionKey;
    }

    public function setExtensionKey(string $extensionKey): void
    {
        $this->extensionKey = $extensionKey;
    }
}
