<?php

declare(strict_types=1);

namespace StefanFroemken\ExtKickstarter;

use StefanFroemken\ExtKickstarter\Command\Input\AutoComplete\AutoCompleteInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Normalizer\NormalizerInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Question\QuestionInterface;
use StefanFroemken\ExtKickstarter\Command\Input\Validator\ValidatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Command\CommandCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Controller\Extbase\ExtbaseControllerCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Controller\Native\NativeControllerCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Domain\Model\DomainCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Domain\Repository\RepositoryCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Domain\Validator\ValidatorCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Event\EventCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\EventListener\EventListenerCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Extension\ExtensionCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Middleware\MiddlewareCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Plugin\Extbase\ExtbasePluginCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Plugin\Native\NativePluginCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Property\TypeConverter\TypeConverterCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\SitePackage\SitePackageCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\SiteSet\SiteSetCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\SiteSet\SiteSettingsDefinitionCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Tca\Table\TcaTableCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Test\Environment\TestEnvCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Upgrade\UpgradeWizardCreatorInterface;
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

    $container
        ->registerForAutoconfiguration(AutoCompleteInterface::class)
        ->addTag('ext-kickstarter.input.autoComplete');
    $container
        ->registerForAutoconfiguration(NormalizerInterface::class)
        ->addTag('ext-kickstarter.input.normalizer');
    $container
        ->registerForAutoconfiguration(QuestionInterface::class)
        ->addTag('ext-kickstarter.input.question');
    $container
        ->registerForAutoconfiguration(ValidatorInterface::class)
        ->addTag('ext-kickstarter.input.validator');
};
