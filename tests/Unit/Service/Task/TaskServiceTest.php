<?php

use App\Service\Task\TaskService;
use App\Entity\Task;
use App\Entity\TimeEntry;
use App\Repository\TaskRepository;
use App\Tests\TestBase;
use Doctrine\ORM\EntityManagerInterface;

class TaskServiceTest extends TestBase
{
    private $entityManager;
    private $taskRepository;
    private $taskService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);

        $this->taskService = new TaskService($this->entityManager, $this->taskRepository);
    }

    public function testStartTask()
    {
        $taskName = 'Test Task';

        $existingTask = new Task();
        $existingTask->setName($taskName);

        $this->taskRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $taskName])
            ->willReturn($existingTask);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(TimeEntry::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->taskService->startTask($taskName);
    }

    public function testStopTask()
    {
        $taskName = 'Test Task';

        $task = new Task();
        $timeEntry = new TimeEntry();

        $this->taskRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $taskName])
            ->willReturn($task);

        $task->addTimeEntry($timeEntry);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(TimeEntry::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->taskService->stopTask($taskName);
    }
}
