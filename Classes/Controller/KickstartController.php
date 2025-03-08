<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\ExtKickstarter\Model\Graph;
use StefanFroemken\ExtKickstarter\Model\Input;
use StefanFroemken\ExtKickstarter\Model\Link;
use StefanFroemken\ExtKickstarter\Model\Output;
use StefanFroemken\ExtKickstarter\Model\Node;
use StefanFroemken\ExtKickstarter\Service\BuildExtensionService;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerInterface;

/**
 * Controller to show an overview about the models
 */
class KickstartController implements ControllerInterface
{
    private const NODE_TYPE_MAPPING = [
        'TYPO3/Extension' => Node\Typo3\ExtensionNode::class,
        'TYPO3/Author' => Node\Typo3\AuthorNode::class,
        'Extbase/Controller' => Node\Extbase\ControllerNode::class,
        'Extbase/ControllerAction' => Node\Extbase\ControllerActionNode::class,
        'Extbase/Module' => Node\Extbase\ModuleNode::class,
        'Extbase/Plugin' => Node\Extbase\PluginNode::class,
        'Extbase/OverwritePluginControllerActionMapping' => Node\Extbase\OverwritePluginControllerActionMappingNode::class,
    ];

    public function __construct(
        readonly private ModuleTemplateFactory $moduleTemplateFactory,
        readonly private BuildExtensionService $buildExtensionService
    ) {}

    public function processRequest(ServerRequestInterface $request): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($request);

        return $moduleTemplate->renderResponse('Kickstarter.html');
    }

    public function build(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return $this->buildExtensionService->build(
                $this->buildGraphTree(\json_decode((string)$request->getBody(), true))
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function buildGraphTree(array $graph): Graph
    {
        $graphTree = new Graph();

        foreach ($graph['nodes'] as $nodeGraph) {
            $inputs = new \SplObjectStorage();
            foreach ($nodeGraph['inputs'] as $inputGraph) {
                $inputs->attach(new Input($inputGraph['link'], $inputGraph['type'], $inputGraph['name']));
            }

            $outputs = new \SplObjectStorage();
            foreach ($nodeGraph['outputs'] as $outputGraph) {
                $outputs->attach(new Output($outputGraph['links'], $outputGraph['type'], $outputGraph['name']));
            }

            $properties = $nodeGraph['properties'] ?? [];

            // Skip invalid nodes
            if (($nodeGraph['type'] ?? '') === '') {
                throw new \InvalidArgumentException('Node must have a type defined');
            }
            if ((int)($nodeGraph['id'] ?? 0) === 0) {
                throw new \InvalidArgumentException('Node must have an ID. NodeType: ' . $nodeGraph['type']);
            }

            $className = self::NODE_TYPE_MAPPING[$nodeGraph['type']];
            $node = new $className(
                (int)$nodeGraph['id'],
                $nodeGraph['type'],
                $nodeGraph['title'],
                $inputs,
                $outputs,
                $properties,
                $graphTree
            );

            $graphTree->addNode($node);
        }

        foreach ($graph['links'] as $link) {
            $graphTree->addLink(new Link($link[0], $link[1], $link[3], $link[5]));
        }

        return $graphTree;
    }
}
