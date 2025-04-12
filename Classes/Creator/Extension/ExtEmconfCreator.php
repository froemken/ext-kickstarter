<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Extension;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ExpressionStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;

class ExtEmconfCreator implements ExtensionCreatorInterface
{
    use FileStructureBuilderTrait;

    private BuilderFactory $factory;

    public function __construct()
    {
        $this->factory = new BuilderFactory();
    }

    public function create(ExtensionInformation $extensionInformation): void
    {
        $extEmconfFilePath = $extensionInformation->getExtensionPath() . 'ext_emconf.php';
        $fileStructure = $this->buildFileStructure($extEmconfFilePath);

        if (!is_file($extEmconfFilePath)) {
            $this->setExtEmconfConfiguration($fileStructure, $extensionInformation);
            file_put_contents($extEmconfFilePath, $fileStructure->getFileContents());
        }
    }

    private function setExtEmconfConfiguration(FileStructure $fileStructure, ExtensionInformation $configurator): void
    {
        $configuration = [
            'title' => $configurator->getTitle(),
            'description' => $configurator->getDescription(),
            'category' => $configurator->getCategory(),
            'state' => $configurator->getState(),
            'author' => $configurator->getAuthor(),
            'author_email' => $configurator->getAuthorEmail(),
            'author_company' => $configurator->getAuthorCompany(),
            'version' => $configurator->getVersion(),
            'constraints' => [
                'depends' => [
                    'typo3' => '13.4.0-13.4.99',
                ],
                'conflicts' => [],
                'suggests' => [],
            ],
        ];

        $fileStructure->addExpressionStructure(new ExpressionStructure(new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\ArrayDimFetch(
                $this->factory->var('EM_CONF'),
                $this->factory->var('_EXTKEY'),
            ),
            $this->factory->val($configuration),
        ))));
    }
}
