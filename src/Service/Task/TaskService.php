<?php

namespace App\Service\Task;

use App\Entity\Task;
use App\Entity\TimeEntry;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService implements TaskServiceInterface
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    public function startTask(string $taskName): void
    {
        if (empty($taskName)) {
            throw new \InvalidArgumentException('Task name cannot be null');
        }

        $existingTask = $this->taskRepository->findOneBy(['name' => $taskName]);
        if ($existingTask) {
            $task = $existingTask;
        } else {
            $task = new Task();
            $task->setName($taskName);
            $task->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($task);
        }

        $timeEntry = new TimeEntry();
        $timeEntry->setTask($task);
        $timeEntry->setStartTime(new \DateTimeImmutable());
        
        $this->entityManager->persist($timeEntry);
        $this->entityManager->flush();
    }

    public function stopTask(string $taskName): void
    {
        $task = $this->taskRepository->findOneBy(['name' => $taskName]);
        if ($task) {
            $now = new \DateTimeImmutable();
            $task->setUpdatedAt($now);
            
            $currentTimeEntry = $task->getCurrentTimeEntry();
            if ($currentTimeEntry) {
                $currentTimeEntry->setEndTime($now);
                $this->entityManager->persist($currentTimeEntry);
            }
    
            $this->entityManager->flush();
        }
    }
}
