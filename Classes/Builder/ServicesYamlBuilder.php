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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for Services.yaml
 */
class ServicesYamlBuilder implements BuilderInterface
{
    public function build(Graph $graph, string $extPath): void
    {
        if ($graph->getExtensionNode()->hasClasses() === false) {
            return;
        }

        $configurationPath = $extPath . '/Configuration/';
        GeneralUtility::mkdir_deep($configurationPath);

        file_put_contents(
            $configurationPath . 'Services.yaml',
            $this->getFileContent($graph)
        );
    }

    private function getFileContent(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();

        return str_replace(
            [
                '{{NAMESPACE}}',
            ],
            [
                $extensionNode->getNamespacePrefix() . '\\',
            ],
            $this->getTemplate()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
services:
  _defaults:
    autowire: true
    autoconfigure: true
    public: false

  {{NAMESPACE}}:
    resource: '../Classes/*'
    exclude:
      - '../Classes/Domain/Model/*'
EOT;
    }
}
