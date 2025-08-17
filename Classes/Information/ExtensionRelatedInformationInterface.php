<?php

namespace FriendsOfTYPO3\Kickstarter\Information;

interface ExtensionRelatedInformationInterface extends InformationInterface
{
    public function getExtensionInformation(): ?ExtensionMappingInformation;

    public function setExtensionInformation(ExtensionMappingInformation $extensionMappingInformation): void;
}
