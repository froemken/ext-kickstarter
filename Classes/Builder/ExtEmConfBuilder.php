<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder;

use StefanFroemken\ExtKickstarter\Model\AbstractNode;
use StefanFroemken\ExtKickstarter\Model\Graph;

/**
 * Get file content for ext_emconf.php
 */
class ExtEmConfBuilder implements BuilderInterface
{
    public function build(Graph $graph, string $extPath): void
    {
        file_put_contents(
            $extPath . 'ext_emconf.php',
            $this->getFileContent($graph)
        );
    }

    private function getFileContent(Graph $graph): string
    {
        $extensionNode = $graph->getExtensionNode();
        $authors = $this->getAuthors($extensionNode->getAuthorNodes());

        return str_replace(
            [
                '{{TITLE}}',
                '{{DESCRIPTION}}',
                '{{AUTHOR_NAME}}',
                '{{AUTHOR_EMAIL}}',
                '{{AUTHOR_COMPANY}}',
                '{{VERSION}}',
            ],
            [
                $extensionNode->getTitle() ?? '',
                $extensionNode->getDescription(),
                $authors['name'],
                $authors['email'],
                $authors['company'],
                $extensionNode->getProperties()['version'] ?? '0.0.0',
            ],
            $this->getTemplate()
        );
    }

    private function getAuthors(\SplObjectStorage $authorNodes): array
    {
        $authors = [
            'name' => [],
            'email' => [],
            'company' => [],
            'role' => [],
        ];

        /** @var AbstractNode $authorNode */
        foreach ($authorNodes as $authorNode) {
            $authorProperties = $authorNode->getProperties();

            $authors['name'][] = $authorProperties['name'] ?? '';
            $authors['email'][] = $authorProperties['email'] ?? '';
            $authors['company'][] = $authorProperties['company'] ?? '';
            $authors['role'][] = $authorProperties['role'] ?? '';
        }

        foreach ($authors as $property => $values) {
            $authors[$property] = implode(',', $values);
        }

        return $authors;
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php
$EM_CONF[$_EXTKEY] = [
    'title' => '{{TITLE}}',
    'description' => '{{DESCRIPTION}}',
    'category' => 'module',
    'state' => 'stable',
    'author' => '{{AUTHOR_NAME}}',
    'author_email' => '{{AUTHOR_EMAIL}}',
    'author_company' => '{{AUTHOR_COMPANY}}',
    'version' => '{{VERSION}}',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
EOT;
    }
}
