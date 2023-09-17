<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getTaskSummary(\DateTimeInterface $date): array
    {
        $tasks = $this->createQueryBuilder('t')
            ->leftJoin('t.timeEntries', 'te')
            ->where('te.startTime >= :startDate')
            ->andWhere('te.startTime < :endDate')
            ->setParameter('startDate', $date->format('Y-m-d 00:00:00'))
            ->setParameter('endDate', $date->format('Y-m-d 23:59:59'))
            ->getQuery()
            ->getResult();

        $summary = [];
        
        foreach ($tasks as $task) {
            $totalTaskTimeWorked = $task->calculateTotalElapsedTime();

            $summary[] = [
                'taskName' => $task->getName(),
                'totalTimeWorked' => $totalTaskTimeWorked,
                'totalTimeWorkedFormatted' => gmdate("H:i:s", $totalTaskTimeWorked)
            ];
        }

        return $summary;
    }
}
