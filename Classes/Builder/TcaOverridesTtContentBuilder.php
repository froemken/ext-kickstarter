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
use StefanFroemken\ExtKickstarter\Traits\WrapTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for Configuration/TCA/Overrides/tt_content.php
 */
class TcaOverridesTtContentBuilder implements BuilderInterface
{
    use WrapTrait;

    public function build(Graph $graph, string $extPath): void
    {
        $tcaOverridesPath = $extPath . '/Configuration/TCA/Overrides/';
        GeneralUtility::mkdir_deep($tcaOverridesPath);

        $fileContent = $this->getFileContent($graph);
        if ($fileContent === '') {
            return;
        }

        file_put_contents($tcaOverridesPath . 'tt_content.php', $fileContent);
    }

    private function getFileContent(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();
        if ($extensionNode->getExtbasePluginNodes()->count() === 0) {
            return '';
        }

        $pluginLines = explode(chr(10), $this->getTemplate());
        foreach ($extensionNode->getExtbasePluginNodes() as $pluginNode) {
            $definition = str_replace(
                [
                    '{{EXTENSION_NAME}}',
                    '{{PLUGIN_NAME}}',
                ],
                [
                    $extensionNode->getExtensionName(),
                    $pluginNode->getPluginName(),
                ],
                $this->getTemplateForPlugin()
            );
            array_push($pluginLines, ...explode(chr(10), $definition));
            $pluginLines[] = '';
        }

        array_pop($pluginLines);

        return implode(chr(10), $pluginLines);
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

EOT;
    }

    private function getTemplateForPlugin(): string
    {
        return <<<'EOT'
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    '{{EXTENSION_NAME}}',
    '{{PLUGIN_NAME}}',
    '{{PLUGIN_NAME}}'
);
EOT;
    }
}
