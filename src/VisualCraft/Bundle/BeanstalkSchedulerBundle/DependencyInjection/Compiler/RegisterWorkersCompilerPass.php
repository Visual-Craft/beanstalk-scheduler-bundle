<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterWorkersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $workersMap = $container->getParameterBag()->get('visual_craft_beanstalk_scheduler.workers_map');

        foreach ($workersMap as $queueId => $workerId) {
            $container->findDefinition("visual_craft_beanstalk_scheduler.scheduler.{$queueId}")
                ->addMethodCall('setWorker', [new Reference($workerId)])
            ;
        }
    }
}
