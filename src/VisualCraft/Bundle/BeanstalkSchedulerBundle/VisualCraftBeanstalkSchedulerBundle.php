<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use VisualCraft\Bundle\VisualCraftBeanstalkBundle\DependencyInjection\Compiler\RegisterWorkersCompilerPass;

class VisualCraftBeanstalkSchedulerBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterWorkersCompilerPass());
    }
}
