<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

use FriendsOfTYPO3\Kickstarter\Configuration\ExtConf;
use FriendsOfTYPO3\Kickstarter\Information\InformationInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtensionMappingValidator implements ValidatorInterface
{
    public function __construct(
        private ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function __invoke(mixed $answer, InformationInterface $information, array $context = []): string
    {
        if (!is_string($answer)) {
            throw new \RuntimeException('Extension key must be set', 9942149847);
        }
        if (!in_array($answer, ExtConf::create($this->extensionConfiguration)->getAvailableExtensions())) {
            throw new \RuntimeException('No extension name ' . $answer . ' found. ', 7065123062);
        }
        return $answer;
    }
}
