<?php

declare(strict_types=1);

namespace StefanFroemken\ExtKickstarter;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $container) {
    $container
        ->registerForAutoconfiguration(Creator\Controller\Extbase\ExtbaseControllerCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.controller.extbase');
    $container
        ->registerForAutoconfiguration(Creator\Controller\Native\NativeControllerCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.controller.native');
    $container
        ->registerForAutoconfiguration(Creator\Domain\Model\DomainCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.domain.model');
    $container
        ->registerForAutoconfiguration(Creator\Domain\Repository\RepositoryCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.domain.repository');
    $container
        ->registerForAutoconfiguration(Creator\Event\EventCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.event');
    $container
        ->registerForAutoconfiguration(Creator\EventListener\EventListenerCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.event-listener');
    $container
        ->registerForAutoconfiguration(Creator\Extension\ExtensionCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.extension');
    $container
        ->registerForAutoconfiguration(Creator\Plugin\Extbase\ExtbasePluginCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.plugin.extbase');
    $container
        ->registerForAutoconfiguration(Creator\Plugin\Native\NativePluginCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.plugin.native');
    $container
        ->registerForAutoconfiguration(Creator\Property\TypeConverter\TypeConverterCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.property.type-converter');
    $container
        ->registerForAutoconfiguration(Creator\Tca\Table\TcaTableCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.tca.table');
    $container
        ->registerForAutoconfiguration(Creator\Test\Environment\TestEnvCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.test.env');
    $container
        ->registerForAutoconfiguration(Creator\Upgrade\UpgradeWizardCreatorInterface::class)
        ->addTag('ext-kickstarter.creator.upgrade-wizard');
};
