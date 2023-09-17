<?php

namespace App\Controller;

use App\Form\TaskFormType;
use App\Service\Task\TaskServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private TaskServiceInterface $taskService;

    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
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
        $taskName = $request->request->get('name');
        
        try {
            $this->taskService->startTask($taskName);
            return $this->json(['message' => 'Task started successfully'], Response::HTTP_OK);
        } 
        catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $this->taskService->startTask($taskName);

        return $this->json(['message' => 'Task started successfully'], Response::HTTP_OK);
    }

    #[Route("/stop", name: "task_stop", methods: ["POST"])]
    public function stopTask(Request $request): Response
    {
        $taskName = $request->request->get('name');

        try {
            $this->taskService->stopTask($taskName);
            return $this->json(['message' => 'Task stopped successfully', Response::HTTP_OK]);
        } 
        catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
