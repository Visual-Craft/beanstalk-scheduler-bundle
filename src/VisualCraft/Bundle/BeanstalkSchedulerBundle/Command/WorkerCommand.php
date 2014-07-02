<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VisualCraft\BeanstalkScheduler\Scheduler;

class WorkerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('vc:beanstalk:worker')
            ->addArgument('queue', null, InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument('queue');
        $serviceId = "visual_craft_beanstalk_scheduler.scheduler.{$queue}";

        if (empty($queue) || !$this->getContainer()->has($serviceId)) {
            $output->writeln("<error>Queue '{$queue}' is not configured</error>");

            return 1;
        }

        /** @var Scheduler $scheduler */
        $scheduler = $this->getContainer()->get($serviceId);
        $scheduler->process();

        return 0;
    }
}
