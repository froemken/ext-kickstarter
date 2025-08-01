<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Upgrade;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Return_;
use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\UpgradeWizardInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ClassStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\MethodStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\NamespaceStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpgradeWizardCreator implements UpgradeWizardCreatorInterface
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(
        NodeFactory $nodeFactory,
        private readonly FileManager $fileManager,
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(UpgradeWizardInformation $upgradeWizardInformation): void
    {
        GeneralUtility::mkdir_deep($upgradeWizardInformation->getUpgradeWizardPath());

        $upgradeWizardFilePath = $upgradeWizardInformation->getUpgradeWizardFilePath();
        $fileStructure = $this->buildFileStructure($upgradeWizardFilePath);

        if (is_file($upgradeWizardFilePath)) {
            $upgradeWizardInformation->getCreatorInformation()->fileExists(
                $upgradeWizardFilePath,
                sprintf(
                    'UpgradeWizards can only be created, not modified. The file %s already exists and cannot be overridden. ',
                    $upgradeWizardInformation->getUpgradeWizardFilename()
                )
            );
            return;
        }
        $this->addClassNodes($fileStructure, $upgradeWizardInformation);
        $this->fileManager->createFile($upgradeWizardFilePath, $fileStructure->getFileContents(), $upgradeWizardInformation->getCreatorInformation());
    }

    private function addClassNodes(FileStructure $fileStructure, UpgradeWizardInformation $upgradeWizardInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('TYPO3\CMS\Install\Attribute\UpgradeWizard'))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite'))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('TYPO3\CMS\Install\Updates\UpgradeWizardInterface'))
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $upgradeWizardInformation->getNamespace(),
                $upgradeWizardInformation->getExtensionInformation(),
            ))
        );
        $fileStructure->addClassStructure(
            new ClassStructure(
                $this->builderFactory
                    ->class($upgradeWizardInformation->getUpgradeWizardClassName())
                    ->addAttribute($this->builderFactory->attribute(
                        'UpgradeWizard',
                        [
                            'identifier' => $upgradeWizardInformation->getUpgradeWizardIdentifier(),
                        ]
                    ))
                    ->makeFinal()
                    ->implement('UpgradeWizardInterface')
                    ->getNode(),
            )
        );
        $fileStructure->addMethodStructure(
            new MethodStructure(
                $this->builderFactory
                    ->method('getTitle')
                    ->makePublic()
                    ->setReturnType('string')
                    ->addStmt(new Return_($this->builderFactory->val('Title of this upgrade wizard')))
                    ->getNode()
            )
        );
        $fileStructure->addMethodStructure(
            new MethodStructure(
                $this->builderFactory
                    ->method('getDescription')
                    ->makePublic()
                    ->setReturnType('string')
                    ->addStmt(new Return_($this->builderFactory->val('Description of this upgrade wizard')))
                    ->getNode()
            )
        );
        $fileStructure->addMethodStructure(
            new MethodStructure(
                $this->builderFactory
                    ->method('executeUpdate')
                    ->makePublic()
                    ->setReturnType('bool')
                    ->addStmt(new Return_($this->builderFactory->val(true)))
                    ->getNode()
            )
        );
        $fileStructure->addMethodStructure(
            new MethodStructure(
                $this->builderFactory
                    ->method('updateNecessary')
                    ->makePublic()
                    ->setReturnType('bool')
                    ->addStmt(new Return_($this->builderFactory->val(true)))
                    ->getNode()
            )
        );
        $fileStructure->addMethodStructure(
            new MethodStructure(
                $this->builderFactory
                    ->method('getPrerequisites')
                    ->makePublic()
                    ->setReturnType('array')
                    ->addStmt(new Return_(
                        $this->builderFactory->val([
                            $this->builderFactory->classConstFetch('DatabaseUpdatedPrerequisite', 'class'),
                        ])
                    ))
                    ->getNode()
            )
        );
    }
}
