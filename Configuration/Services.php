<?php

declare(strict_types=1);

namespace StefanFroemken\ExtKickstarter;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $container) {
    $container->registerForAutoconfiguration(Builder\BuilderInterface::class)->addTag('ext-kickstarter.builder');
};
