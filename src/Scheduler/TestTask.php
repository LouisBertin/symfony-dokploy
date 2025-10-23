<?php

namespace App\Scheduler;

use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsSchedule('test')]
class TestTask implements ScheduleProviderInterface
{
    public function __construct(
        private MessageBusInterface $bus
    ) {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every('10 minutes', new TestMessage())
            );
    }
}

class TestMessage
{
    public function __construct()
    {
    }
}
