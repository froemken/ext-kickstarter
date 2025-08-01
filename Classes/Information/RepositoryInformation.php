<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

readonly class RepositoryInformation
{
    private const DOMAIN_REPOSITORY_PATH = 'Classes/Domain/Repository/';

    public function __construct(
        private ExtensionInformation $extensionInformation,
        private string $repositoryClassName,
        private CreatorInformation $creatorInformation = new CreatorInformation()
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getRepositoryClassName(): string
    {
        return $this->repositoryClassName;
    }

    public function getRepositoryFilename(): string
    {
        return $this->repositoryClassName . '.php';
    }

    public function getRepositoryFilePath(): string
    {
        return $this->getRepositoryPath() . $this->getRepositoryFilename();
    }

    public function getRepositoryPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::DOMAIN_REPOSITORY_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Domain\\Repository';
    }

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }
}
