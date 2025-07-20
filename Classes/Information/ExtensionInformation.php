<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionInformation
{
    protected const SYSTEM_COLUMNS = [
        'endtime',
        'starttime',
        'hidden',
        'sys_language_uid',
        'l10n_parent',
        'l10n_diffsource',
    ];

    private const TCA_PATH = 'Configuration/TCA/';

    private const CONTROLLER_PATH = 'Classes/Controller/';

    private const TCA_OVERRIDES_PATH = 'Configuration/TCA/Overrides/';

    public function __construct(
        private readonly string $extensionKey,
        private readonly string $composerPackageName,
        private readonly string $title,
        private readonly string $description,
        private readonly string $version,
        private readonly string $category,
        private readonly string $state,
        private readonly string $author,
        private readonly string $authorEmail,
        private readonly string $authorCompany,
        private readonly string $namespaceForAutoload,
        private readonly string $extensionPath,
    ) {}

    public function getExtensionKey(): string
    {
        return $this->extensionKey;
    }

    public function getExtensionName(): string
    {
        return GeneralUtility::underscoredToUpperCamelCase($this->extensionKey);
    }

    public function getComposerPackageName(): string
    {
        return $this->composerPackageName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    public function getAuthorCompany(): string
    {
        return $this->authorCompany;
    }

    public function getNamespaceForAutoload(): string
    {
        // In general "\\\\" is correct as this would result in MyVendor\\MyExt\\ normally.
        // BUT: As we are using JSON_UNESCAPED_SLASHES while building the composer.json file
        // the "\\\\" will be applied as it is. So, we have to remove the escaping here.
        return str_replace('\\\\', '\\', $this->namespaceForAutoload);
    }

    /**
     * Return a namespace prefix which you can use within your extension classes for "namespace XY"
     */
    public function getNamespacePrefix(): string
    {
        return $this->getNamespaceForAutoload();
    }

    public function getTableNamePrefix(): string
    {
        return sprintf(
            'tx_%s_domain_model_',
            str_replace('_', '', $this->getExtensionKey()),
        );
    }

    public function getExtensionPath(): string
    {
        return $this->extensionPath;
    }

    public function getControllerPath(): string
    {
        return $this->getExtensionPath() . self::CONTROLLER_PATH;
    }

    public function getTcaPath(): string
    {
        return $this->getExtensionPath() . self::TCA_PATH;
    }

    public function getTcaOverridesPath(): string
    {
        return $this->getExtensionPath() . self::TCA_OVERRIDES_PATH;
    }

    public function getFilePathForTcaTable(string $tcaTableName): string
    {
        return $this->getTcaPath() . $tcaTableName . '.php';
    }

    public function getFilePathForController(string $classname): string
    {
        return $this->getControllerPath() . $classname . '.php';
    }

    public function getControllerClassnames(): array
    {
        $controllerPath = $this->getControllerPath();
        if (!is_dir($controllerPath)) {
            return [];
        }

        $controllerClasses = [];
        foreach (scandir($controllerPath) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $controllerClasses[] = pathinfo($file, PATHINFO_FILENAME);
        }

        sort($controllerClasses);

        return $controllerClasses;
    }

    public function getExtbaseControllerClassnames(): array
    {
        $nodeFinder = new NodeFinder();
        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        $extbaseControllerClassnames = [];
        foreach ($this->getControllerClassnames() as $controllerClassname) {
            $stmts = $parser->parse(file_get_contents($this->getFilePathForController($controllerClassname)));
            $classNode = $nodeFinder->findFirst($stmts, static function (Node $node): bool {
                return $node instanceof Node\Stmt\Class_
                    && $node->extends instanceof Node\Name
                    && $node->extends->toString() === 'ActionController';
            });

            if ($classNode instanceof Node\Stmt\Class_) {
                $extbaseControllerClassnames[] = $classNode->name->name;
            }
        }

        sort($extbaseControllerClassnames);

        return $extbaseControllerClassnames;
    }

    public function getExtbaseControllerActionNames(string $extbaseControllerClassname): array
    {
        $nodeFinder = new NodeFinder();
        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        $extbaseControllerActionNames = [];

        $stmts = $parser->parse(file_get_contents($this->getFilePathForController($extbaseControllerClassname)));
        $classMethodNodes = $nodeFinder->find($stmts, static function (Node $node): bool {
            return $node instanceof Node\Stmt\ClassMethod
                && $node->isPublic()
                && str_ends_with($node->name->name, 'Action');
        });

        foreach ($classMethodNodes as $classMethodNode) {
            if ($classMethodNode instanceof Node\Stmt\ClassMethod) {
                $extbaseControllerActionNames[] = $classMethodNode->name->name;
            }
        }

        sort($extbaseControllerActionNames);

        return $extbaseControllerActionNames;
    }

    public function getConfiguredTcaTables(): array
    {
        $configuredTcaTables = [];
        $tcaPath = $this->getTcaPath();
        if (!is_dir($tcaPath)) {
            return $configuredTcaTables;
        }

        foreach (scandir($tcaPath) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $tcaPath . '/' . $file;

            if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                $configuredTcaTables[] = pathinfo($filePath, PATHINFO_FILENAME);
            }
        }

        sort($configuredTcaTables);

        return $configuredTcaTables;
    }

    public function getTcaForTable(string $tableName): array
    {
        $tcaTableFilePath = $this->getFilePathForTcaTable($tableName);
        if (!is_file($tcaTableFilePath)) {
            return [];
        }

        return require $tcaTableFilePath;
    }

    public function getColumnNamesFromTca(array $tableTca): array
    {
        $columnNames = array_keys($tableTca['columns']);

        sort($columnNames);

        return $columnNames;
    }

    public function getSystemColumnNamesFromTca(array $tableTca): array
    {
        return $this->filterColumns($tableTca, true);
    }

    public function getDomainColumnNamesFromTca(array $tableTca): array
    {
        return $this->filterColumns($tableTca, false);
    }

    private function filterColumns(array $tableTca, bool $systemColumns): array
    {
        $allColumns = $this->getColumnNamesFromTca($tableTca);

        return array_values(array_filter($allColumns, function ($column) use ($systemColumns) {
            $isSystem = in_array($column, self::SYSTEM_COLUMNS, true);
            return $systemColumns ? $isSystem : !$isSystem;
        }));
    }
}
