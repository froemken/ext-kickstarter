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
use StefanFroemken\ExtKickstarter\Model\AbstractNode;
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\PluginNode;
use StefanFroemken\ExtKickstarter\Traits\WrapTrait;

/**
 * Get file content for ext_localconf.php
 */
class ExtLocalConfBuilder implements BuilderInterface
{
    use WrapTrait;

    public function build(Graph $graph, string $extPath): void
    {
        $fileContent = $this->getFileContent($graph);
        if ($fileContent === '') {
            return;
        }

        file_put_contents($extPath . 'ext_localconf.php', $fileContent);
    }

    private function getFileContent(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();
        if ($extensionNode->getExtbasePluginNodes()->count() === 0) {
            return '';
        }

        return str_replace(
            [
                '{{COMPOSER_NAME}}',
                '{{EXTBASE_PLUGINS}}',
            ],
            [
                $extensionNode->getComposerName(),
                $this->getExtbasePluginContent($graph),
            ],
            $this->getTemplate()
        );
    }

    private function getExtbasePluginContent(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();
        $extbasePluginNodes = $extensionNode->getExtbasePluginNodes();

        $plugins = [];
        foreach ($extbasePluginNodes as $extbasePluginNode) {
            $pluginLines = [
                '\'' . $extensionNode->getExtensionName() . '\',',
                '\'' . ($extbasePluginNode->getProperties()['pluginName'] ?? '') . '\',',
            ];
            array_push($pluginLines, ...$this->getExtbaseControllerActionDefinitionLines($extbasePluginNode, false));
            array_push($pluginLines, ...$this->getExtbaseControllerActionDefinitionLines($extbasePluginNode, true));
            $pluginLines[] = $this->getPluginType($extbasePluginNode) . ',';

            $plugins[] = implode(
                chr(10),
                $this->wrap(
                    $pluginLines,
                    ['\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('],
                    [');'],
                    2
                )
            );
        }

        return implode(chr(10), $plugins);
    }

    private function getExtbaseControllerActionDefinitionLines(PluginNode $extbasePluginNode, bool $isUncached): array
    {
        $controllerActionDefinition = $extbasePluginNode->getControllerActionDefinitionStrings($isUncached);
        if ($controllerActionDefinition === []) {
            return ['[],'];
        }

        return $this->wrap(
            $controllerActionDefinition,
            ['['],
            ['],'],
            1
        );
    }

    private function getPluginType(AbstractNode $extbasePluginNode): string
    {
        $pluginType = '\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN';
        if ($extbasePluginNode->getProperties()['pluginType'] === 'content') {
            $pluginType = '\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT';
        }

        return $pluginType;
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

{{EXTBASE_PLUGINS}}
EOT;
    }
}
