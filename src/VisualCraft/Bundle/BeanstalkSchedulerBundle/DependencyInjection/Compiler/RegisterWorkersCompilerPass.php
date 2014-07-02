<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VisualCraft\BeanstalkScheduler\WorkerInterface;

class RegisterWorkersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $workersMap = $container->getParameterBag()->get('visual_craft_beanstalk_scheduler.workers_map');

        foreach ($workersMap as $queueId => $workerId) {
            $schedulerDefinition = $container->findDefinition("visual_craft_beanstalk_scheduler.scheduler.{$queueId}");

            if (!$container->hasDefinition($workerId)) {
                throw new \Exception("Worker with id '{$workerId}' not found for queue '{$queueId}' description");
            }

            if (!is_subclass_of($container->getDefinition($workerId)->getClass(), WorkerInterface::class)) {
                throw new \Exception("Worker with id '{$workerId}' should implement interface '\\VisualCraft\\BeanstalkScheduler\\WorkerInterface'");
            }

            $schedulerDefinition
                ->addMethodCall(
                    'registerWorker',
                    array(new Reference($workerId))
                )
            ;
        }
    }
}
