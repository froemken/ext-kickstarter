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
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\ControllerNode;

/**
 * Get file content for ext_localconf.php
 */
class ExtLocalConfBuilder implements BuilderInterface
{
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
            $controllerNodes = $extbasePluginNode->getControllerNodes();

            $pluginLines = [
                '\'' . $extensionNode->getExtensionName() . '\',',
                '\'' . ($extbasePluginNode->getProperties()['pluginName'] ?? '') . '\',',
            ];
            array_push($pluginLines, ...$this->getExtbaseControllerActionDefinitionLines($controllerNodes, false));
            array_push($pluginLines, ...$this->getExtbaseControllerActionDefinitionLines($controllerNodes, true));
            $pluginLines[] = $this->getPluginType($extbasePluginNode) . ',';

            $plugins[] = implode(
                chr(10),
                $this->wrap(
                    $pluginLines,
                    '\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(',
                    ');',
                    2
                )
            );
        }

        return implode(chr(10), $plugins);
    }

    /**
     * @param \SplObjectStorage|ControllerNode[] $controllerNodes
     */
    private function getExtbaseControllerActionDefinitionLines(
        \SplObjectStorage $controllerNodes,
        bool $isUncached
    ): array {
        $controllerActionDefinition = [];
        foreach ($controllerNodes as $controllerNode) {
            $controllerActionDefinition[] = $controllerNode->getControllerActionDefinitionString($isUncached);
        }

        if ($controllerActionDefinition === []) {
            return ['[],'];
        }

        return $this->wrap($controllerActionDefinition, '[', '],', 1);
    }

    private function wrap(array $lines, string $before, string $after, int $indents): array
    {
        $indent = '    ';

        $indentBefore = str_repeat($indent, $indents - 1);
        $indentLines = str_repeat($indent, $indents);
        $indentAfter = str_repeat($indent, $indents - 1);

        foreach ($lines as $key => $line) {
            $lines[$key] = $indentLines . $line;
        }

        $indentedLines = [
            $indentBefore . $before,
        ];

        array_push($indentedLines, ...$lines);
        $indentedLines[] = $indentAfter . $after;

        return $indentedLines;
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

/*
 * This file is part of the package {{COMPOSER_NAME}}.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function (): void {
{{EXTBASE_PLUGINS}}
});
EOT;
    }
}
