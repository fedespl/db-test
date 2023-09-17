<?php

use App\Repository\TaskRepository;
use App\Service\Summary\SummaryService;
use App\Tests\TestBase;

class SummaryServiceTest extends TestBase
{
    private $taskRepository;
    private $summaryService;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);

        $this->summaryService = new SummaryService($this->taskRepository);
    }

    public function testGetTaskSummary()
    {
        // Mock the taskRepository to return some dummy data
        $dummyTaskSummaries = [
            ['totalTimeWorked' => 3600], // 1 hour
            ['totalTimeWorked' => 1800], // 30 minutes
        ];

        $this->taskRepository->expects($this->once())
            ->method('getTaskSummary')
            ->willReturn($dummyTaskSummaries);

        $result = $this->summaryService->getTaskSummary();

        $this->assertEquals(5400, $result['totalTimeWorkedInDay']); // 1 hour and 30 minutes
        $this->assertEquals($dummyTaskSummaries, $result['tasks']);
    }
}
