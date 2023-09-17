<?php

namespace App\Controller;

use App\Form\TaskFormType;
use App\Service\Task\TaskServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private TaskServiceInterface $taskService;
    private LoggerInterface $logger;

    public function __construct(TaskServiceInterface $taskService, LoggerInterface $logger)
    {
        $this->taskService = $taskService;
        $this->logger = $logger;
    }

    #[Route("/", name: "task_index")]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TaskFormType::class);

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/start", name: "task_start", methods: ["POST"])]
    public function start(Request $request): Response
    {
        // $this->logger->info('Request Name:', ['request NAME::' => $request->request->get('name')]);
        $taskName = $request->request->get('name');
        $this->taskService->startTask($taskName);

        return $this->json(['message' => 'Task started successfully']);
    }

    #[Route("/stop", name: "task_stop", methods: ["POST"])]
    public function stopTask(Request $request): Response
    {
        $taskName = $request->request->get('name');
        $this->taskService->stopTask($taskName);

        return $this->json(['message' => 'Task stopped successfully']);
    }

    #[Route("/summary", name: "task_summary")]
    public function summary(): Response
    {
        $summary = $this->taskService->getTaskSummary();

        return $this->render('summary.html.twig', [
            'summary' => $summary,
        ]);
    }
}
