<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service;

use Psr\Http\Message\ResponseInterface;
use StefanFroemken\ExtKickstarter\Information\ControllerInformation;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Information\ModelInformation;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\Information\RepositoryInformation;
use StefanFroemken\ExtKickstarter\Information\TableInformation;
use StefanFroemken\ExtKickstarter\Model\Graph;
use StefanFroemken\ExtKickstarter\Model\Node\Main\AuthorNode;
use StefanFroemken\ExtKickstarter\Model\Node\Main\ExtensionNode;
use StefanFroemken\ExtKickstarter\Service\Creator\ControllerCreatorService;
use StefanFroemken\ExtKickstarter\Service\Creator\ExtensionCreatorService;
use StefanFroemken\ExtKickstarter\Service\Creator\ModelCreatorService;
use StefanFroemken\ExtKickstarter\Service\Creator\PluginCreatorService;
use StefanFroemken\ExtKickstarter\Service\Creator\RepositoryCreatorService;
use StefanFroemken\ExtKickstarter\Service\Creator\TableCreatorService;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use TYPO3\CMS\Core\Http\JsonResponse;

readonly class BuildExtensionService
{
    use ExtensionInformationTrait;

    public function __construct(
        private ExtensionCreatorService $extensionCreatorService,
        private PluginCreatorService $pluginCreatorService,
        private ControllerCreatorService $controllerCreatorService,
        private RepositoryCreatorService $repositoryCreatorService,
        private TableCreatorService $tableCreatorService,
        private ModelCreatorService $modelCreatorService,
    ) {}

    public function build(Graph $graph): ResponseInterface
    {
        if (!$this->validate($graph)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Graph is missing an extension node',
            ]);
        }

        $this->generateExtensionFiles($graph);

        return new JsonResponse([
            'status' => 'ok',
            'message' => 'Extension was created successfully',
        ]);
    }

    private function validate(Graph $graph): bool
    {
        // Extension node is a must-have
        return $graph->getExtensionNode() instanceof ExtensionNode;
    }

    private function generateExtensionFiles(Graph $graph): void
    {
        $this->extensionCreatorService->create($this->getExtensionInformation($graph));
        $this->createPlugins($graph);
        $this->createControllers($graph);
        $this->createRepositories($graph);
        $this->createTcaTables($graph);
        $this->createModels($graph);
    }

    private function getExtensionInformation(Graph $graph): ExtensionInformation
    {
        $extensionNode = $graph->getExtensionNode();
        $authorNode = $extensionNode->getAuthorNodes()->current();

        return new ExtensionInformation(
            $extensionNode->getExtensionKey(),
            $extensionNode->getComposerName(),
            $extensionNode->getTitle(),
            $extensionNode->getDescription(),
            '0.0.1',
            'plugin',
            'stable',
            $authorNode instanceof AuthorNode ? $authorNode->getAuthorName() : '',
            $authorNode instanceof AuthorNode ? $authorNode->getAuthorEmail() : '',
            $authorNode instanceof AuthorNode ? $authorNode->getAuthorCompany() : '',
            $extensionNode->getNamespaceForAutoload(),
            $this->getExtensionPath($extensionNode->getExtensionKey())
        );
    }

    private function createPlugins(Graph $graph): void
    {
        $extensionNode = $graph->getExtensionNode();
        foreach ($extensionNode->getExtbasePluginNodes() as $extbasePluginNode) {
            $this->pluginCreatorService->create(new PluginInformation(
                $this->getExtensionInformation($graph),
                true,
                $extbasePluginNode->getPluginName(),
                $extbasePluginNode->getPluginName(),
            ));
        }
    }

    private function createControllers(Graph $graph): void
    {
        $extensionNode = $graph->getExtensionNode();
        foreach ($extensionNode->getExtbaseControllerNodes() as $extbaseControllerNode) {
            $actionNames = [];
            foreach ($extbaseControllerNode->getControllerActionNodes() as $actionNode) {
                $actionNames[] = $actionNode->getActionName();
            }

            $this->controllerCreatorService->create(new ControllerInformation(
                $this->getExtensionInformation($graph),
                true,
                $extbaseControllerNode->getControllerName(),
                $actionNames,
            ));
        }
    }

    private function createRepositories(Graph $graph): void
    {
        $extensionNode = $graph->getExtensionNode();
        foreach ($extensionNode->getExtbaseRepositoryNodes() as $extbaseRepositoryNode) {
            $this->repositoryCreatorService->create(new RepositoryInformation(
                $this->getExtensionInformation($graph),
                $extbaseRepositoryNode->getRepositoryName(),
            ));
        }
    }

    private function createTcaTables(Graph $graph): void
    {
        $extensionNode = $graph->getExtensionNode();
        foreach ($extensionNode->getTableNodes() as $tableNode) {
            $columns = [];
            foreach ($tableNode->getColumnNodes() as $columnNode) {
                $columns[$columnNode->getColumnName()] = [
                    'exclude' => true,
                    'label' => $columnNode->getLabel(),
                    'config' => TableCreatorService::TABLE_COLUMN_TYPES[$columnNode->getColumnType()],
                ];
            }

            $this->tableCreatorService->create(new TableInformation(
                $this->getExtensionInformation($graph),
                $tableNode->getTableName(),
                $tableNode->getTitle(),
                $tableNode->getLabel(),
                $columns,
            ));
        }
    }

    private function createModels(Graph $graph): void
    {
        $extensionNode = $graph->getExtensionNode();
        foreach ($extensionNode->getExtbaseRepositoryNodes() as $repositoryNode) {
            $properties = [];
            foreach ($repositoryNode->getTableNode()->getModelProperties() as $propertyNode) {
                $properties[$propertyNode->getColumnName()] = [
                    'propertyName' => $propertyNode->getPropertyName(),
                    'tcaType' => $tableTca['columns'][$propertyNode->getColumnName()]['config']['type'] ?? 'input',
                    'dataType' => $propertyNode->getPropertyDataType(),
                ];
            }

            $this->modelCreatorService->create(new ModelInformation(
                $this->getExtensionInformation($graph),
                $repositoryNode->getModelName(),
                $repositoryNode->getTableName(),
                true,
                $properties,
            ));
        }
    }
}
