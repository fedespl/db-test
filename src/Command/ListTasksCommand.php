<?php

namespace App\Command;

use App\Entity\TimeEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list-tasks',
    description: 'List all tasks with status, start time, end time, and total elapsed time',
)]
class ListTasksCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $timeEntries = $this->entityManager->getRepository(TimeEntry::class)->findAll();

        if (empty($timeEntries)) {
            $io->success('No time entries found.');
            return Command::SUCCESS;
        }

        $headers = ['Task Name', 'Status', 'Start Time', 'End Time', 'Total Elapsed Time'];

        $tableData = [];
        foreach ($timeEntries as $timeEntry) {
            $task = $timeEntry->getTask();
            $status = $timeEntry->getEndTime() ? 'Completed' : 'Running';
            $startTime = $timeEntry->getStartTime()->format('Y-m-d H:i:s');
            $endTime = $timeEntry->getEndTime() ? $timeEntry->getEndTime()->format('Y-m-d H:i:s') : 'N/A';
            $totalTime = $timeEntry->getTotalElapsedTime();

            $tableData[] = [$task->getName(), $status, $startTime, $endTime, $totalTime];
        }

        $io->table($headers, $tableData);

        return Command::SUCCESS;
    }
}
