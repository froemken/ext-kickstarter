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
use StefanFroemken\ExtKickstarter\Model\Node\Extbase\RepositoryNode;
use StefanFroemken\ExtKickstarter\Model\Node\Tca\AbstractColumnNode;
use StefanFroemken\ExtKickstarter\Service\TcaSchemaService;
use StefanFroemken\ExtKickstarter\Traits\GetClassHeaderTrait;
use StefanFroemken\ExtKickstarter\Traits\WrapTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for Extbase Domain Models
 */
class DomainModelBuilder implements BuilderInterface
{
    use GetClassHeaderTrait;
    use WrapTrait;

    public function __construct(
        private readonly TcaSchemaService $tcaSchemaService,
    ) {}

    public function build(Graph $graph, string $extPath): void
    {
        $repositoryNodes = $graph->getExtensionNode()->getExtbaseRepositoryNodes();
        if ($repositoryNodes->count() === 0) {
            return;
        }

        foreach ($repositoryNodes as $repositoryNode) {
            $tableNode = $repositoryNode->getTableNode();
            if ($tableNode === null) {
                continue;
            }

            $columnNodes = $tableNode->getModelProperties();
            if ($columnNodes->count() === 0) {
                continue;
            }

            $domainModelPath = $extPath . '/Classes/Domain/Model/';
            GeneralUtility::mkdir_deep($domainModelPath);

            file_put_contents(
                $domainModelPath . $repositoryNode->getModelFilename(),
                $this->getFileContent($repositoryNode, $columnNodes, $graph)
            );
        }
    }

    /**
     * @param \SplObjectStorage|AbstractColumnNode[] $columnNodes
     */
    private function getFileContent(
        RepositoryNode $repositoryNode,
        \SplObjectStorage $columnNodes,
        Graph $graph
    ): string {
        return str_replace(
            [
                '{{CLASS_HEADER}}',
                '{{NAMESPACE}}',
                '{{MODEL_NAME}}',
                '{{PROPERTIES}}',
                '{{METHODS}}',
            ],
            [
                $this->getClassHeader($graph->getExtensionNode()),
                $repositoryNode->getModelNamespace(),
                $repositoryNode->getModelName(),
                implode(chr(10), $this->getPropertyLines($columnNodes)),
                implode(chr(10), $this->getMethodLines($columnNodes)),
            ],
            $this->getTemplate()
        );
    }

    /**
     * @param \SplObjectStorage|AbstractColumnNode[] $columnNodes
     */
    private function getPropertyLines(\SplObjectStorage $columnNodes): array
    {
        $propertyLines = [];
        foreach ($columnNodes as $columnNode) {
            $columnInformation = $this->tcaSchemaService->getColumnInformationBasedOnTca(
                $columnNode->getColumnType(),
                $columnNode->getProperties(),
            );

            $default = $columnInformation['default'] ?? '';
            if ($default === '') {
                $default = '\'\'';
            }

            $columnType = 'string';
            if (in_array($columnInformation['columnType'], ['INT', 'SMALLINT', 'TINYINT', 'BIGINT'], true)) {
                $columnType = 'int';
            } elseif ($columnInformation['columnType'] === 'DECIMAL') {
                $columnType = 'float';
            }

            $propertyLines[] = sprintf(
                '    protected %s $%s = %s;',
                $columnType,
                $columnNode->getPropertyName(),
                $default,
            );
            $propertyLines[] = '';
        }

        return $propertyLines;
    }

    /**
     * @param \SplObjectStorage|AbstractColumnNode[] $columnNodes
     */
    private function getMethodLines(\SplObjectStorage $columnNodes): array
    {
        $methodLines = [];
        foreach ($columnNodes as $columnNode) {
            $columnInformation = $this->tcaSchemaService->getColumnInformationBasedOnTca(
                $columnNode->getColumnType(),
                $columnNode->getProperties(),
            );

            $columnType = 'string';
            if (in_array($columnInformation['columnType'], ['INT', 'SMALLINT', 'TINYINT', 'BIGINT'], true)) {
                $columnType = 'int';
            } elseif ($columnInformation['columnType'] === 'DECIMAL') {
                $columnType = 'float';
            }

            $definition = str_replace(
                [
                    '{{PROPERTY_NAME}}',
                    '{{UC_PROPERTY_NAME}}',
                    '{{DATA_TYPE}}',
                ],
                [
                    $columnNode->getPropertyName(),
                    ucfirst($columnNode->getPropertyName()),
                    $columnType,
                ],
                $this->getTemplateForGetterSetter()
            );

            array_push($methodLines, ...$this->wrap(explode(chr(10), $definition), [], [], 1));
            $methodLines[] = '';
        }

        array_pop($methodLines);

        return $methodLines;
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

declare(strict_types=1);

{{CLASS_HEADER}}

namespace {{NAMESPACE}};

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class {{MODEL_NAME}} extends AbstractEntity
{
{{PROPERTIES}}
{{METHODS}}
}
EOT;
    }

    private function getTemplateForGetterSetter(): string
    {
        return <<<'EOT'
public function get{{UC_PROPERTY_NAME}}(): {{DATA_TYPE}}
{
    return $this->{{PROPERTY_NAME}};
}

public function set{{UC_PROPERTY_NAME}}({{DATA_TYPE}} ${{PROPERTY_NAME}}): void
{
    $this->{{PROPERTY_NAME}} = ${{PROPERTY_NAME}};
}
EOT;
    }
}
