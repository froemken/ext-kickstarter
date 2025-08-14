<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

class ModelInformation
{
    private const DOMAIN_MODEL_PATH = 'Classes/Domain/Model/';

    private const CLASSES_FILE_PATH = 'Configuration/Extbase/Persistence/Classes.php';

    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
        private readonly string $modelClassName,
        private readonly string $mappedTableName,
        private readonly bool $abstractEntity,
        private readonly array $properties,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }

    public function getMappedTableName(): string
    {
        return $this->mappedTableName;
    }

    public function isAbstractEntity(): bool
    {
        return $this->abstractEntity;
    }

    public function isExpectedTableName(): bool
    {
        $expectedTableName = sprintf(
            'tx_%s_domain_model_%s',
            str_replace('_', '', $this->extensionInformation->getExtensionKey()),
            strtolower($this->modelClassName),
        );

        return $this->mappedTableName === $expectedTableName;
    }

    public function getClassesFilePath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::CLASSES_FILE_PATH;
    }

    public function getModelFilename(): string
    {
        return $this->modelClassName . '.php';
    }

    public function getModelFilePath(): string
    {
        return $this->getModelPath() . $this->getModelFilename();
    }

    public function getModelPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::DOMAIN_MODEL_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Domain\\Model';
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
