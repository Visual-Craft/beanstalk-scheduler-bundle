<?php

namespace VisualCraft\Bundle\BeanstalkSchedulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use VisualCraft\BeanstalkScheduler\Manager;
use VisualCraft\BeanstalkScheduler\Scheduler;

class ClearQueueCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('vc:beanstalk:clear-queue')
            ->addArgument('queues', InputArgument::IS_ARRAY|InputArgument::REQUIRED)
            ->addOption('ignore-errors', 'i')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $getManagerServiceId = function ($queue) {
            return "visual_craft_beanstalk_scheduler.manager.{$queue}";
        };

        if ($input->hasArgument('queues')) {
            $queues = array_unique($input->getArgument('queues'));
            $missingQueues = [];

            foreach ($queues as $queue) {
                if (!$this->getContainer()->has($getManagerServiceId($queue))) {
                    $missingQueues[] = $queue;
                }
            }

            if ($missingQueues) {
                throw new \InvalidArgumentException(sprintf("Worker queues ['%s'] are not registered", implode("', '", $missingQueues)));
            }
        } else {
            $queues = $this->getContainer()->getParameter('visual_craft_beanstalk_scheduler.queues');
        }

        foreach ($queues as $queue) {
            /** @var Manager $manager */
            $manager = $this->getContainer()->get($getManagerServiceId($queue));
            $manager->clearQueue($input->getOption('ignore-errors'));
        }

        return 0;
    }
}
