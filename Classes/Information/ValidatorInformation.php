<?php

namespace StefanFroemken\ExtKickstarter\Information;

use StefanFroemken\ExtKickstarter\Enums\ValidatorType;

readonly class ValidatorInformation
{
    private const VALIDATOR_PATH = 'Classes/Domain/Validator/';

    private const DATA_TYPES = [
        'int',
        'string',
    ];

    public function __construct(
        private ExtensionInformation $extensionInformation,
        private string $validatorName,
        private ValidatorType $validatorType,
        private ?string $modelName = null,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getValidatorName(): string
    {
        return $this->validatorName;
    }

    public function getValidatorType(): ValidatorType
    {
        return $this->validatorType;
    }

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function getModelFullyQualifiedName(): ?string
    {
        return $this->extensionInformation->getNamespacePrefix() . ModelInformation::NAME_SPACE_PART . '\\' . $this->modelName;
    }

    public function getValidatorFilename(): string
    {
        return $this->validatorName . '.php';
    }

    public function getValidatorFilePath(): string
    {
        return $this->getValidatorPath() . $this->getValidatorFilename();
    }

    public function getValidatorPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::VALIDATOR_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Domain\\Validator';
    }
}
