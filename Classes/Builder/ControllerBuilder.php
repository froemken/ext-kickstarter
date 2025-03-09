<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder;

use StefanFroemken\ExtKickstarter\Model\Graph;
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\ControllerNode;
use StefanFroemken\ExtKickstarter\Traits\GetClassHeaderTrait;
use StefanFroemken\ExtKickstarter\Traits\WrapTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for Extbase Controllers
 */
class ControllerBuilder implements BuilderInterface
{
    use GetClassHeaderTrait;
    use WrapTrait;

    public function build(Graph $graph, string $extPath): void
    {
        $controllerNodes = $graph->getExtensionNode()->getExtbaseControllerNodes();
        if ($controllerNodes->count() === 0) {
            return;
        }

        foreach ($controllerNodes as $controllerNode) {
            $controllerPath = $extPath . '/Classes/Controller/';
            GeneralUtility::mkdir_deep($controllerPath);

            file_put_contents(
                $controllerPath . $controllerNode->getControllerFilename(),
                $this->getFileContent($controllerNode, $graph)
            );
        }
    }

    private function getFileContent(ControllerNode $controllerNode, Graph $graph): string
    {
        return str_replace(
            [
                '{{CLASS_HEADER}}',
                '{{NAMESPACE}}',
                '{{IMPORTS}}',
                '{{CONTROLLER_NAME}}',
                '{{METHODS}}',
            ],
            [
                $this->getClassHeader($graph->getExtensionNode()),
                $controllerNode->getNamespace(),
                implode(chr(10), $this->getImports($controllerNode)),
                $controllerNode->getControllerName(),
                implode(chr(10), $this->getMethods($controllerNode)),
            ],
            $this->getTemplate()
        );
    }

    private function getImports(ControllerNode $controllerNode): array
    {
        $imports = [
            'TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController',
            'Psr\\Http\\Message\\ResponseInterface',
        ];

        foreach ($controllerNode->getRepositoryNodes() as $repositoryNode) {
            $imports[] = $repositoryNode->getNamespace() . '\\' . $repositoryNode->getRepositoryName();
        }

        sort($imports);

        foreach ($imports as $key => $import) {
            $imports[$key] = 'use ' . $import . ';';
        }

        return $imports;
    }

    private function getMethods(ControllerNode $controllerNode): array
    {
        $methodLines = [];
        array_push($methodLines, ...$this->getConstructorLines($controllerNode));
        $methodLines[] = '';
        array_push($methodLines, ...$this->getActionLines($controllerNode));

        return $methodLines;
    }

    private function getConstructorLines(ControllerNode $controllerNode): array
    {
        $repositoryNodes = $controllerNode->getRepositoryNodes();
        if ($repositoryNodes->count() === 0) {
            return [];
        }

        $repositoryLines = [];
        foreach ($repositoryNodes as $repositoryNode) {
            $repositoryLines[] = sprintf(
                'private %s %s,',
                $repositoryNode->getRepositoryName(),
                $repositoryNode->getRepositoryVariableName()
            );
        }

        return $this->wrap(
            $repositoryLines,
            ['public function __construct('],
            [') {}'],
            2
        );
    }

    private function getActionLines(ControllerNode $controllerNode): array
    {
        $controllerActions = $controllerNode->getControllerActionNodes();
        if ($controllerActions->count() === 0) {
            return [];
        }

        $actionMethodLines = [];
        foreach ($controllerActions as $controllerAction) {
            array_push($actionMethodLines, ...$this->wrap(
                [
                    'return $this->htmlResponse();',
                ],
                [
                    sprintf('public function %s(): ResponseInterface', $controllerAction->getActionName()),
                    '{',
                ],
                ['}'],
                2
            ));
            $actionMethodLines[] = '';
        }

        array_pop($actionMethodLines);

        return $actionMethodLines;
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

declare(strict_types=1);

{{CLASS_HEADER}}

namespace {{NAMESPACE}};

{{IMPORTS}}

class {{CONTROLLER_NAME}} extends ActionController
{
{{METHODS}}
}
EOT;
    }
}
