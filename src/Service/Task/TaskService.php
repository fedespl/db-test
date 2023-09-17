<?php

namespace App\Service\Task;

use App\Entity\Task;
use App\Entity\TimeEntry;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TaskService implements TaskServiceInterface
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->logger = $logger;
    }

    public function startTask(string $taskName): void
    {
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

    public function getTaskSummary(): array
    {
        $date = new \DateTime();
        $taskSummaries = $this->taskRepository->getTaskSummary($date);

        $totalTimeWorkedInDay = 0;

        foreach ($taskSummaries as $summary) {
            $totalTimeWorkedInDay += $summary['totalTimeWorked'];
        }

        // $this->logger->info('getTaskSummary:', ['taskSummaries::' => $taskSummaries]);

        return [
            'totalTimeWorkedInDay' => $totalTimeWorkedInDay,
            'tasks' => $taskSummaries,
        ];
    }
}
