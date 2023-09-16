<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\TimeEntry;
use App\Tests\TestBase;

class TimeEntryTest extends TestBase
{
    private function createValidTimeEntry(): TimeEntry
    {
        $task = new Task();
        $task->setName('Valid Task Name');

        return (new TimeEntry())
            ->setTask($task)
            ->setStartTime(new \DateTime())
            ->setEndTime(new \DateTime('+1 hour'));
    }

    public function testValidTimeEntry()
    {
        $timeEntry = $this->createValidTimeEntry();

        $errors = $this->validator->validate($timeEntry);
        $this->assertCount(0, $errors);
    }

    public function testNullTask()
    {
        $timeEntry = $this->createValidTimeEntry();
        $timeEntry->setTask(null);

        $errors = $this->validator->validate($timeEntry);
        $this->assertCount(1, $errors);
    }

    public function testEndTimeBeforeStartTime()
    {
        $timeEntry = $this->createValidTimeEntry();
        $timeEntry->setStartTime(new \DateTime('+2 hours'));
        $timeEntry->setEndTime(new \DateTime('+1 hour'));

        $errors = $this->validator->validate($timeEntry);
        $this->assertCount(1, $errors);
    }
}
