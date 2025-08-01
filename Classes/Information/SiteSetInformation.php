<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

readonly class SiteSetInformation
{
    private const CONFIG_FILENAME = 'config.yaml';

    public function __construct(
        private ExtensionInformation $extensionInformation,
        private string $identifier,
        private string $path,
        private string $label = '',
        private array $dependencies = [],
        private bool $hidden = false,
        private CreatorInformation $creatorInformation = new CreatorInformation()
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getSiteSetFilePath(): string
    {
        return $this->getSiteSetPath() . self::CONFIG_FILENAME;
    }

    public function getSiteSetPath(): string
    {
        return $this->extensionInformation->getSetPath() . $this->getPath() . '/';
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Domain\\SiteSet';
    }

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }
}
