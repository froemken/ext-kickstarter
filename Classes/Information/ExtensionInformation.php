<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

class ExtensionInformation
{
    public function __construct(
        private readonly string $extensionKey,
        private readonly string $composerPackageName,
        private readonly string $title,
        private readonly string $description,
        private readonly string $version,
        private readonly string $category,
        private readonly string $state,
        private readonly string $author,
        private readonly string $authorEmail,
        private readonly string $authorCompany,
        private readonly string $namespacePrefix,
        private readonly string $extensionPath,
    ) {}

    public function getExtensionKey(): string
    {
        return $this->extensionKey;
    }

    public function getComposerPackageName(): string
    {
        return $this->composerPackageName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    public function getAuthorCompany(): string
    {
        return $this->authorCompany;
    }

    public function getNamespacePrefix(): string
    {
        return $this->namespacePrefix;
    }

    public function getExtensionPath(): string
    {
        return $this->extensionPath;
    }
}