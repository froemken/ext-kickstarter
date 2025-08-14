<?php

declare(strict_types=1);

namespace FriendsOfTYPO3\Kickstarter;

use FriendsOfTYPO3\Kickstarter\Creator\Command\CommandCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Controller\Extbase\ExtbaseControllerCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Controller\Native\NativeControllerCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Domain\Model\DomainCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Domain\Repository\RepositoryCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Domain\Validator\ValidatorCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Event\EventCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\EventListener\EventListenerCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Extension\ExtensionCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Middleware\MiddlewareCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Plugin\Extbase\ExtbasePluginCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Plugin\Native\NativePluginCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Property\TypeConverter\TypeConverterCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\SitePackage\SitePackageCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\SiteSet\SiteSetCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\SiteSet\SiteSettingsDefinitionCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Tca\Table\TcaTableCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Test\Environment\TestEnvCreatorInterface;
use FriendsOfTYPO3\Kickstarter\Creator\Upgrade\UpgradeWizardCreatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $container): void {
    $container
        ->registerForAutoconfiguration(CommandCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.command');
    $container
        ->registerForAutoconfiguration(ExtbaseControllerCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.controller.extbase');
    $container
        ->registerForAutoconfiguration(NativeControllerCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.controller.native');
    $container
        ->registerForAutoconfiguration(DomainCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.domain.model');
    $container
        ->registerForAutoconfiguration(RepositoryCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.domain.repository');
    $container
        ->registerForAutoconfiguration(ValidatorCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.domain.validator');
    $container
        ->registerForAutoconfiguration(EventCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.event');
    $container
        ->registerForAutoconfiguration(EventListenerCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.event-listener');
    $container
        ->registerForAutoconfiguration(ExtensionCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.extension');
    $container
        ->registerForAutoconfiguration(MiddlewareCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.middleware');
    $container
        ->registerForAutoconfiguration(ExtbasePluginCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.plugin.extbase');
    $container
        ->registerForAutoconfiguration(NativePluginCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.plugin.native');
    $container
        ->registerForAutoconfiguration(SitePackageCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.property.site-package');
    $container
        ->registerForAutoconfiguration(SiteSetCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.site-set');
    $container
        ->registerForAutoconfiguration(SiteSettingsDefinitionCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.site-settings-definition');
    $container
        ->registerForAutoconfiguration(TypeConverterCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.property.type-converter');
    $container
        ->registerForAutoconfiguration(TcaTableCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.tca.table');
    $container
        ->registerForAutoconfiguration(TestEnvCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.test.env');
    $container
        ->registerForAutoconfiguration(UpgradeWizardCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.upgrade-wizard');
};
