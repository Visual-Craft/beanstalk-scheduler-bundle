<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use VisualCraft\BeanstalkScheduler\Scheduler;

class RunSchedulerCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('vc:beanstalk:run-scheduler')
            ->addArgument('queue', InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument('queue');
        $serviceId = "visual_craft_beanstalk_scheduler.scheduler.{$queue}";

        if (!$this->container->has($serviceId)) {
            throw new \InvalidArgumentException(sprintf("Scheduler with id '%s' does not exist.", $queue));
        }

        /** @var Scheduler $scheduler */
        $scheduler = $this->container->get($serviceId);
        $scheduler->process();

        return 0;
    }
}
