<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model\Node\Extbase;

use StefanFroemken\ExtKickstarter\Model\AbstractNode;

class RepositoryNode extends AbstractNode
{
    public function getRepositoryName(): string
    {
        return $this->getProperties()['repositoryName'] ?? '';
    }

    public function getRepositoryFilename(): string
    {
        return $this->getRepositoryName() . '.php';
    }

    public function getRepositoryVariableName(): string
    {
        return '$' . lcfirst($this->getRepositoryName());
    }

    public function getModelName(): string
    {
        return substr($this->getRepositoryName(), 0, -10);
    }

    public function getTableName(): string
    {
        $tableName = $this->getProperties()['tableName'] ?? '';

        if ($tableName === '') {
            $tableName = sprintf(
                '%s_%s_%s',
                $this->graph->getExtensionNode()->getTablePrefix(),
                'domain_model',
                strtolower($this->getModelName())
            );
        }

        return $tableName;
    }

    public function getNamespace(): string
    {
        return sprintf(
            '%s\\%s',
            $this->graph->getExtensionNode()->getNamespacePrefix(),
            'Domain\\Repository'
        );
    }
}
