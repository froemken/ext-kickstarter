<?php

namespace FriendsOfTYPO3\Kickstarter\Information;

interface ExtensionRelatedInformationInterface extends InformationInterface
{
    public function getExtensionInformation(): ?ExtensionInformation;

    public function setExtensionInformation(ExtensionInformation $extensionInformation): void;
}
