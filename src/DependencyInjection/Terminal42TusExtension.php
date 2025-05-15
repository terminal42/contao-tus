<?php

declare(strict_types=1);

namespace Terminal42\TusBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Terminal42\TusBundle\Cron\ExpirationCron;

class Terminal42TusExtension extends ConfigurableExtension
{
    /**
     * @param array{
     *     upload_dir: string,
     *     expires?: int
     * } $mergedConfig
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container->setParameter('terminal42_tus.upload_dir', $mergedConfig['upload_dir']);

        if ($mergedConfig['expires']) {
            $cron = new Definition(ExpirationCron::class);
            $cron->setArgument(0, new Reference('terminal42_tus.cache'));
            $cron->addTag('contao.cronjob', ['interval' => $mergedConfig['expires']]);
            $container->setDefinition('terminal42_tus.cron', $cron);
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config'),
        );

        $loader->load('services.yaml');
    }
}
