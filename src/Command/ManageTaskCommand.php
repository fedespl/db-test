<?php

namespace App\Command;

use App\Service\Task\TaskServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:manage-task',
    description: 'Start or end a task',
)]
class ManageTaskCommand extends Command
{
    private TaskServiceInterface $taskService;

    public function __construct(TaskServiceInterface $taskService)
    {
        parent::__construct();

        $this->taskService = $taskService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::REQUIRED, 'Action: start or stop')
            ->addArgument('taskName', InputArgument::REQUIRED, 'Name of the task');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');
        $taskName = $input->getArgument('taskName');

        try 
        {
            if ($action === 'start') {
                $this->taskService->startTask($taskName);
                $io->success("Task '$taskName' started successfully.");
    
            } 
            elseif ($action === 'stop') {
                $this->taskService->stopTask($taskName);
                $io->success("Task '$taskName' stopped successfully.");
    
            } else {
                throw new \InvalidArgumentException("Invalid action: Use 'start' or 'stop'.");
            }
        } 
        catch (\Exception $e) 
        {
            $io->error("Error: " . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
