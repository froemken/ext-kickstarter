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
use StefanFroemken\ExtKickstarter\Builder\BuilderInterface;
use StefanFroemken\ExtKickstarter\Model\Graph;
use StefanFroemken\ExtKickstarter\Model\AbstractNode;
use StefanFroemken\ExtKickstarter\Model\Node\Typo3\ExtensionNode;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BuildExtensionService
{
    public function __construct(private readonly iterable $builders) {}

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

    private function getExtPath(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();
        $extPath = sprintf(
            '%s/%s/%s',
            Environment::getPublicPath(),
            'typo3temp/ext-kickstarter',
            ($extensionNode->getProperties()['extensionKey'] ?? 'my_extension')
        );

        if (is_dir($extPath)) {
            GeneralUtility::rmdir($extPath, true);
        }

        GeneralUtility::mkdir_deep($extPath);

        return $extPath . '/';
    }

    private function generateExtensionFiles(Graph $graph): void
    {
        $extPath = $this->getExtPath($graph);

        /** @var BuilderInterface $builder */
        foreach ($this->builders as $builder) {
            $builder->build($graph, $extPath);
        }
    }
}
