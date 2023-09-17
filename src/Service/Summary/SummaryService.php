<?php

namespace App\Service\Summary;

use App\Repository\TaskRepository;

class SummaryService implements SummaryServiceInterface
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getTaskSummary(): array
    {
        $date = new \DateTime();
        $taskSummaries = $this->taskRepository->getTaskSummary($date);

        $totalTimeWorkedInDay = 0;

        foreach ($taskSummaries as $summary) {
            $totalTimeWorkedInDay += $summary['totalTimeWorked'];
        }

        return [
            'totalTimeWorkedInDay' => $totalTimeWorkedInDay,
            'totalTimeWorkedInDayFormatted' => gmdate("H:i:s", $totalTimeWorkedInDay),
            'tasks' => $taskSummaries,
        ];
    }
}
