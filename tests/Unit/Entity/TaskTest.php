<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\TimeEntry;
use App\Tests\TestBase;
use Symfony\Component\Validator\ConstraintViolationList;

class TaskTest extends TestBase
{
    private function createValidTask(): Task
    {
        return (new Task())
            ->setName('Valid Task Name');
    }

    public function testValidTask()
    {
        $task = $this->createValidTask();

        $errors = $this->validator->validate($task);
        $this->assertCount(0, $errors);
        $this->assertNotEmpty($task->getCreatedAt());
    }

    public function testBlankName()
    {
        $task = $this->createValidTask();
        $task->setName('');

        $errors = $this->validator->validate($task);
        $this->assertCount(1, $errors);
        $this->assertInstanceOf(ConstraintViolationList::class, $errors);
        $this->assertSame('name', $errors[0]->getPropertyPath());
        $this->assertSame('This value should not be blank.', $errors[0]->getMessage());
    }

    public function testNameLengthExceedsLimit()
    {
        $task = $this->createValidTask();
        $task->setName(str_repeat('A', 256));

        $errors = $this->validator->validate($task);
        $this->assertCount(1, $errors);
        $this->assertInstanceOf(\Symfony\Component\Validator\ConstraintViolationList::class, $errors);
    }

    public function testGetCurrentTimeEntryWithNoTimeEntries()
    {
        $task = $this->createValidTask();

        $currentTimeEntry = $task->getCurrentTimeEntry();

        $this->assertNull($currentTimeEntry);
    }

    public function testGetCurrentTimeEntryWithActiveTimeEntry()
    {
        $task = $this->createValidTask();
        $timeEntry = new TimeEntry();
        $task->addTimeEntry($timeEntry);

        $currentTimeEntry = $task->getCurrentTimeEntry();

        $this->assertSame($timeEntry, $currentTimeEntry);
    }

    public function testTaskHasMultipleTimeEntries()
    {
        $task = $this->createValidTask();

        $timeEntry1 = new TimeEntry();
        $task->addTimeEntry($timeEntry1);

        $timeEntry2 = new TimeEntry();
        $task->addTimeEntry($timeEntry2);
        
        $this->assertCount(2, $task->getTimeEntries());
    }
}