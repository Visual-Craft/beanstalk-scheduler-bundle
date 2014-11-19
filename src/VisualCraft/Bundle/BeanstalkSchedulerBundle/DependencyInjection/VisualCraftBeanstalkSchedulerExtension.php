<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VisualCraftBeanstalkSchedulerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->registerConnections($container, $config['connections']);
        $this->registerQueues($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $connectionsConfig
     */
    private function registerConnections(ContainerBuilder $container, $connectionsConfig)
    {
        foreach ($connectionsConfig as $connectionId => $connectionConfig) {
            $connectionDefinition = new Definition(
                'Pheanstalk\Pheanstalk',
                [$connectionConfig['host'], $connectionConfig['port'], $connectionConfig['connectTimeout']]
            );
            $connectionDefinition->setPublic(false);
            $container->setDefinition(
                "visual_craft_beanstalk_scheduler.connection.{$connectionId}",
                $connectionDefinition
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function registerQueues(ContainerBuilder $container, $config)
    {
        $workersMap = [];

        foreach ($config['queues'] as $queueId => $queueConfig) {
            $connectionReference = new Reference("visual_craft_beanstalk_scheduler.connection.{$queueConfig['connection']}");

            $managerDefinition = new DefinitionDecorator('visual_craft_beanstalk_scheduler.abstract_manager');
            $managerDefinition
                ->setClass('VisualCraft\BeanstalkScheduler\Manager')
                ->setArguments([$connectionReference, $config['queue_prefix'] . $queueId])
            ;
            $container->setDefinition("visual_craft_beanstalk_scheduler.manager.{$queueId}", $managerDefinition);

            $schedulerDefinition = new DefinitionDecorator('visual_craft_beanstalk_scheduler.abstract_scheduler');
            $schedulerDefinition
                ->setClass('VisualCraft\BeanstalkScheduler\Scheduler')
                ->setArguments([$connectionReference, $config['queue_prefix'] . $queueId])
                ->addMethodCall('setReschedule', [$queueConfig['reschedule']])
            ;
            $container->setDefinition("visual_craft_beanstalk_scheduler.scheduler.{$queueId}", $schedulerDefinition);

            $workersMap[$queueId] = $queueConfig['worker'];
        }

        $container->setParameter('visual_craft_beanstalk_scheduler.workers_map', $workersMap);
    }
}
