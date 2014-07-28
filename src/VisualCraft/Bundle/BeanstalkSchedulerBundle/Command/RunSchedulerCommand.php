<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VisualCraft\BeanstalkScheduler\Scheduler;

class RunSchedulerCommand extends ContainerAwareCommand
{
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

        if (!$this->getContainer()->has($serviceId)) {
            throw new \InvalidArgumentException(sprintf("Scheduler with id '%s' does not exist.", $queue));
        }

        /** @var Scheduler $scheduler */
        $scheduler = $this->getContainer()->get($serviceId);
        $scheduler->process();

        return 0;
    }
}
