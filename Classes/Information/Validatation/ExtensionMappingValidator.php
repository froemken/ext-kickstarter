<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validatation;

use FriendsOfTYPO3\Kickstarter\Command\Input\Validator\ValidatorInterface;
use FriendsOfTYPO3\Kickstarter\Configuration\ExtConf;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtensionMappingValidator implements ValidatorInterface
{
    public function __construct(
        private ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function __invoke(mixed $answer): string
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
