<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\DependencyInjection\Compiler;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InjectContainerToCommandsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $containerReference = $container->findDefinition(ContainerInterface::class)
            ? new Reference(ContainerInterface::class)
            : new Reference('container')
        ;

        $container
            ->getDefinition('visual_craft_beanstalk_scheduler.command.clear_queue')
            ->addMethodCall('setContainer', [$containerReference])
        ;
        $container
            ->getDefinition('visual_craft_beanstalk_scheduler.command.run_scheduler')
            ->addMethodCall('setContainer', [$containerReference])
        ;
    }
}
