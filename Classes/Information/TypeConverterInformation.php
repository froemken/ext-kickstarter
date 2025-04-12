<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

readonly class TypeConverterInformation
{
    private const TYPE_CONVERTER_PATH = 'Classes/Property/TypeConverter/';

    public function __construct(
        private ExtensionInformation $extensionInformation,
        private string $typeConverterClassName,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getTypeConverterClassName(): string
    {
        return $this->typeConverterClassName;
    }

    public function getTypeConverterFilename(): string
    {
        return $this->typeConverterClassName . '.php';
    }

    public function getTypeConverterFilePath(): string
    {
        return $this->getTypeConverterPath() . $this->getTypeConverterFilename();
    }

    public function getTypeConverterPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::TYPE_CONVERTER_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Property\\TypeConverter';
    }
}
