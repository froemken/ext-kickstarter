<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\PhpParser;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;

class NodeFactory
{
    private BuilderFactory $factory;

    public function __construct()
    {
        $this->factory = new BuilderFactory();
    }

    /**
     * Creates a strict_type declaration node which will be rendered as:
     *
     * declare(strict_types=1);
     */
    public function createDeclareStrictTypes(): Node\Stmt\Declare_
    {
        return new Node\Stmt\Declare_([
            new Node\Stmt\DeclareDeclare(
                'strict_types',
                new Node\Scalar\LNumber(1)
            ),
        ]);
    }

    /**
     * Creates a use import node which will be rendered as:
     *
     * use TYPO3\CMS\Core\Utility\GeneralUtility;
     */
    public function createUseImport(string $useImport): Node\Stmt\Use_
    {
        return $this->factory
            ->use($useImport)
            ->getNode();
    }

    /**
     * Creates a namespace node which will be rendered as:
     *
     * namespace MyVendor\MyExt\Domain\Model;
     *
     * Don't forget to add the "use" and "class" nodes to this resulting node: $namespaceNode->stmts[]
     */
    public function createNamespace(string $namespace, ExtensionInformation $extensionInformation): Node\Stmt\Namespace_
    {
        $docComment = <<<'EOT'
/*
 * This file is part of the package stefanfroemken/blog-example.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
EOT;

        return $this->factory
            ->namespace($namespace)
            ->setDocComment(
                str_replace('{PACKAGE_NAME}', $extensionInformation->getComposerPackageName(), $docComment)
            )
            ->getNode();
    }

    /**
     * Creates a class node which will be rendered as:
     *
     * class BlogController {}
     *
     * Don't forget to add the "classConst", "trait", "property" and "method" nodes to this resulting node:
     * $classNode->stmts[]
     *
     * It is YOUR job to also register the use imports with ::createUseImport()
     */
    public function createClass(string $className): Node\Stmt\Class_
    {
        return $this->factory
            ->class($className)
            ->getNode();
    }

    /**
     * Creates a use trait node which will be rendered as:
     *
     * use GetMapHelperTrait;
     *
     * It is YOUR job to also register its use import with ::createUseImport()
     */
    public function createUseTrait(string $traitName): Node\Stmt\TraitUse
    {
        // In ->getNode() only "Node" was registered as a return value. Adding TraitUse here, too
        /** @var Node|Node\Stmt\TraitUse $node */
        $node = $this->factory
            ->useTrait($traitName)
            ->getNode();

        return $node;
    }

    /**
     * Creates a class const node which will be rendered as:
     *
     * public TEMPLATE = 'EXT:my_ext/Resources/Private/Templates/Page.html';
     */
    public function createClassConst(string $classConstName, mixed $value): Node\Stmt\ClassConst
    {
        return $this->factory
            ->classConst($classConstName, $value)
            ->getNode();
    }

    /**
     * Creates a class property node which will be rendered as:
     *
     * protected string $address = '';
     */
    public function createProperty(string $propertyName): Node\Stmt\Property
    {
        return $this->factory
            ->property($propertyName)
            ->makeProtected()
            ->getNode();
    }

    /**
     * Creates a class method node which will be rendered as:
     *
     * public function getFillColor(): string
     * {
     *     return $this->fillColor;
     * }
     */
    public function createMethod(string $methodName): Node\Stmt\ClassMethod
    {
        return $this->factory
            ->method($methodName)
            ->makePublic()
            ->getNode();
    }

    /*
     * BELOW YOU WILL FIND HELPER METHODS FOR SPECIFIC TASKS
     */

    /**
     * Creates an extbase controller class node which will be rendered as:
     *
     * class BlogController extends ActionController {}
     *
     * Don't forget to add the "classConst", "trait", "property" and "method" nodes to this resulting node:
     * $classNode->stmts[]
     *
     * It is YOUR job to also register the use imports with ::createUseImport()
     */
    public function createExtbaseControllerClass(string $className): Node\Stmt\Class_
    {
        return $this->factory
            ->class($className)
            ->extend('ActionController')
            ->getNode();
    }

    /**
     * Creates a non extbase based controller action method node which will be rendered as:
     *
     * public function showAction(): ResponseInterface
     * {
     *     return new HtmlResponse('Hello World!');
     * }
     */
    public function createControllerActionMethod(string $methodName): Node\Stmt\ClassMethod
    {
        return $this->factory
            ->method($methodName)
            ->makePublic()
            ->setReturnType('ResponseInterface')
            ->addStmts([
                new Node\Stmt\Return_(
                    new Node\Expr\New_(
                        new Node\Name('HtmlResponse'),
                        [
                            new Node\Arg(new Node\Scalar\String_('Hello World!'))
                        ]
                    )
                ),
            ])
            ->getNode();
    }

    /**
     * Creates an extbase based controller action method node which will be rendered as:
     *
     * public function showAction(): ResponseInterface
     * {
     *     return $this->htmlResponse();
     * }
     */
    public function createExtbaseControllerActionMethod(string $methodName): Node\Stmt\ClassMethod
    {
        return $this->factory
            ->method($methodName)
            ->makePublic()
            ->setReturnType('ResponseInterface')
            ->addStmts([
                new Node\Stmt\Return_(
                    new Node\Expr\MethodCall(
                        new Node\Expr\Variable('this'),
                        'htmlResponse'
                    )
                )
            ])
            ->getNode();
    }
}