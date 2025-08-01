<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Extension;

use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;

class PhpStormMetaCreator implements ExtensionCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(ExtensionInformation $extensionInformation): void
    {
        $this->fileManager->createOrModifyFile(
            $extensionInformation->getExtensionPath() . '.phpstorm.meta.php',
            $this->getTemplate(),
            $extensionInformation->getCreatorInformation()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

/**
 * Extend PhpStorms code completion capabilities by providing a meta file
 *
 * @link https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html
 */

namespace PHPSTORM_META {
    expectedArguments(
        \TYPO3\CMS\Core\Context\Context::getAspect(),
        0,
        'date',
        'visibility',
        'backend.user',
        'frontend.user',
        'workspace',
        'language',
        'frontend.preview',
    );
    override(\TYPO3\CMS\Core\Context\Context::getAspect(), map([
        'date' => \TYPO3\CMS\Core\Context\DateTimeAspect::class,
        'visibility' => \TYPO3\CMS\Core\Context\VisibilityAspect::class,
        'backend.user' => \TYPO3\CMS\Core\Context\UserAspect::class,
        'frontend.user' => \TYPO3\CMS\Core\Context\UserAspect::class,
        'workspace' => \TYPO3\CMS\Core\Context\WorkspaceAspect::class,
        'language' => \TYPO3\CMS\Core\Context\LanguageAspect::class,
        'frontend.preview' => \TYPO3\CMS\Frontend\Context\PreviewAspect::class,
    ]));
    expectedArguments(
        \TYPO3\CMS\Core\Context\DateTimeAspect::get(),
        0,
        'timestamp',
        'iso',
        'timezone',
        'full',
        'accessTime'
    );
    expectedArguments(
        \TYPO3\CMS\Core\Context\VisibilityAspect::get(),
        0,
        'includeHiddenPages',
        'includeHiddenContent',
        'includeDeletedRecords'
    );
    expectedArguments(
        \TYPO3\CMS\Core\Context\UserAspect::get(),
        0,
        'id',
        'username',
        'isLoggedIn',
        'isAdmin',
        'groupIds',
        'groupNames'
    );
    expectedArguments(
        \TYPO3\CMS\Core\Context\WorkspaceAspect::get(),
        0,
        'id',
        'isLive',
        'isOffline'
    );
    expectedArguments(
        \TYPO3\CMS\Core\Context\LanguageAspect::get(),
        0,
        'id',
        'contentId',
        'fallbackChain',
        'overlayType',
        'legacyLanguageMode',
        'legacyOverlayType'
    );
    expectedArguments(
        \TYPO3\CMS\Frontend\Context\PreviewAspect::get(),
        0,
        'isPreview'
    );

    expectedArguments(
        \Psr\Http\Message\ServerRequestInterface::getAttribute(),
        0,
        'frontend.user',
        'normalizedParams',
        'site',
        'language',
        'routing',
        'module',
        'moduleData',
        'frontend.controller',
        'frontend.typoscript',
        'frontend.cache.collector',
        'frontend.cache.instruction',
        'frontend.page.information',
    );
    override(\Psr\Http\Message\ServerRequestInterface::getAttribute(), map([
        'frontend.user' => \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class,
        'normalizedParams' => \TYPO3\CMS\Core\Http\NormalizedParams::class,
        'site' => \TYPO3\CMS\Core\Site\Entity\SiteInterface::class,
        'language' => \TYPO3\CMS\Core\Site\Entity\SiteLanguage::class,
        'routing' => '\TYPO3\CMS\Core\Routing\SiteRouteResult|\TYPO3\CMS\Core\Routing\PageArguments',
        'module' => \TYPO3\CMS\Backend\Module\ModuleInterface::class,
        'moduleData' => \TYPO3\CMS\Backend\Module\ModuleData::class,
        'frontend.controller' => \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class,
        'frontend.typoscript' => \TYPO3\CMS\Core\TypoScript\FrontendTypoScript::class,
        'frontend.cache.collector' => \TYPO3\CMS\Core\Cache\CacheDataCollector::class,
        'frontend.cache.instruction' => \TYPO3\CMS\Frontend\Cache\CacheInstruction::class,
        'frontend.page.information' => \TYPO3\CMS\Frontend\Page\PageInformation::class,
    ]));

    expectedArguments(
        \TYPO3\CMS\Core\Http\ServerRequest::getAttribute(),
        0,
        'frontend.user',
        'normalizedParams',
        'site',
        'language',
        'routing',
        'module',
        'moduleData'
    );
    override(\TYPO3\CMS\Core\Http\ServerRequest::getAttribute(), map([
        'frontend.user' => \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class,
        'normalizedParams' => \TYPO3\CMS\Core\Http\NormalizedParams::class,
        'site' => \TYPO3\CMS\Core\Site\Entity\SiteInterface::class,
        'language' => \TYPO3\CMS\Core\Site\Entity\SiteLanguage::class,
        'routing' => '\TYPO3\CMS\Core\Routing\SiteRouteResult|\TYPO3\CMS\Core\Routing\PageArguments',
        'module' => \TYPO3\CMS\Backend\Module\ModuleInterface::class,
        'moduleData' => \TYPO3\CMS\Backend\Module\ModuleData::class,
    ]));

    override(\TYPO3\CMS\Core\Routing\SiteMatcher::matchRequest(), type(
            \TYPO3\CMS\Core\Routing\SiteRouteResult::class,
            \TYPO3\CMS\Core\Routing\RouteResultInterface::class,
        )
    );

    override(\TYPO3\CMS\Core\Routing\PageRouter::matchRequest(), type(
        \TYPO3\CMS\Core\Routing\PageArguments::class,
        \TYPO3\CMS\Core\Routing\RouteResultInterface::class,
    ));

    override(\Psr\Container\ContainerInterface::get(0), map([
        '' => '@',
    ]));

    override(\Psr\EventDispatcher\EventDispatcherInterface::dispatch(0), map([
        '' => '@',
    ]));

    override(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(0), map([
        '' => '@'
    ]));
}
EOT;
    }
}
