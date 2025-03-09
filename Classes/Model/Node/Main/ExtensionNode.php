<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model\Node\Main;

use StefanFroemken\ExtKickstarter\Model\AbstractNode;
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\ControllerNode;
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\ModuleNode;
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\PluginNode;
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\RepositoryNode;
use StefanFroemken\ExtKickstarter\Model\Node\Tca\TableNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionNode extends AbstractNode
{
    public function getExtensionKey(): string
    {
        return trim($this->getProperties()['extensionKey'] ?? '');
    }

    public function getVendorName(): string
    {
        return trim($this->getProperties()['vendorName'] ?? '');
    }

    public function getExtensionName(): string
    {
        return trim($this->getProperties()['extensionName'] ?? '');
    }

    public function getTitle(): string
    {
        return trim($this->getProperties()['title'] ?? '');
    }

    public function getComposerName(): string
    {
        $composerName = trim($this->getProperties()['composerName'] ?? '');

        if ($composerName === '') {
            $vendorName = GeneralUtility::camelCaseToLowerCaseUnderscored($this->getVendorName());
            $extensionName = GeneralUtility::camelCaseToLowerCaseUnderscored($this->getExtensionName());
            $composerName = str_replace('_', '-', $vendorName . '/' . $extensionName);
        }

        return $composerName;
    }

    public function getClassnamePrefix(): string
    {
        return '\\' . $this->getVendorName() . '\\' . $this->getExtensionName();
    }

    public function getNamespacePrefix(): string
    {
        return $this->getVendorName() . '\\' . $this->getExtensionName();
    }

    public function getTablePrefix(): string
    {
        return 'tx_' . str_replace('_', '', $this->getExtensionKey());
    }

    public function hasClasses(): bool
    {
        return $this->getExtbaseControllerNodes()->count()
            || $this->getExtbaseRepositoryNodes()->count();
    }

    /**
     * @return \SplObjectStorage|AuthorNode[]
     */
    public function getAuthorNodes(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName($this, 'authors');
    }

    /**
     * @return \SplObjectStorage|TableNode[]
     */
    public function getTableNodes(): \SplObjectStorage
    {
        return $this->graph->getNodesByType('Tca/Table');
    }

    /**
     * @return \SplObjectStorage|PluginNode[]
     */
    public function getExtbasePluginNodes(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName($this, 'extbasePlugins');
    }

    /**
     * @return \SplObjectStorage|ModuleNode[]
     */
    public function getExtbaseModules(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName($this, 'extbaseModules');
    }

    /**
     * This will return ALL extbase controllers.
     * Regardless of their relation to a plugin.
     *
     * @return \SplObjectStorage|ControllerNode[]
     */
    public function getExtbaseControllerNodes(): \SplObjectStorage
    {
        return $this->graph->getNodesByType('Extbase/Controller');
    }

    /**
     * @return \SplObjectStorage|RepositoryNode[]
     */
    public function getExtbaseRepositoryNodes(): \SplObjectStorage
    {
        return $this->graph->getNodesByType('Extbase/Repository');
    }
}
