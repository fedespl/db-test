<?php

namespace App\Service\Task;

interface TaskServiceInterface
{
    public function startTask(string $taskName): void;
    public function stopTask(string $taskName): void;
    public function getTaskSummary(): array;
}
