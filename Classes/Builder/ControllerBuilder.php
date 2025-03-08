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
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\RepositoryNode;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for Extbase Controllers
 */
class ControllerBuilder implements BuilderInterface
{
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
                '{{COMPOSER_NAME}}',
                '{{NAMESPACE}}',
                '{{MODEL}}',
                '{{CONTROLLER_NAME}}',
            ],
            [
                $graph->getExtensionNode()->getComposerName(),
                $controllerNode->getNamespace(),
                $controllerNode->getModelName(),
                $controllerNode->getControllerName(),
            ],
            $this->getTemplate()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

declare(strict_types=1);

/*
 * This file is part of the package {{COMPOSER_NAME}}.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace {{NAMESPACE}};

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller class to manage {{MODEL}} objects
 */
class {{CONTROLLER_NAME}} extends ActionController
{

}
EOT;
    }
}
