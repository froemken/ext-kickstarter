<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

class UpgradeWizardInformation
{
    private const UPGRADE_WIZARD_PATH = 'Classes/Upgrade/';

    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
        private readonly string $upgradeWizardClassName,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getUpgradeWizardClassName(): string
    {
        return $this->upgradeWizardClassName;
    }

    public function getUpgradeWizardFilename(): string
    {
        return $this->upgradeWizardClassName . '.php';
    }

    public function getUpgradeWizardFilePath(): string
    {
        return $this->getUpgradeWizardPath() . $this->getUpgradeWizardFilename();
    }

    public function getUpgradeWizardPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::UPGRADE_WIZARD_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Upgrade';
    }
}
