<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VisualCraft\Bundle\BeanstalkSchedulerBundle\DependencyInjection\Compiler\InjectContainerToCommandsCompilerPass;
use VisualCraft\Bundle\BeanstalkSchedulerBundle\DependencyInjection\Compiler\RegisterWorkersCompilerPass;

class VisualCraftBeanstalkSchedulerBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterWorkersCompilerPass());
        $container->addCompilerPass(new InjectContainerToCommandsCompilerPass());
    }
}
