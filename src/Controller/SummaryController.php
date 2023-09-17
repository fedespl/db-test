<?php

namespace App\Controller;

use App\Service\Summary\SummaryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SummaryController extends AbstractController
{
    private SummaryServiceInterface $summaryService;

    public function __construct(SummaryServiceInterface $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    #[Route("/summary", name: "task_summary")]
    public function summary(): Response
    {
        $summary = $this->summaryService->getTaskSummary();

        return $this->render('summary.html.twig', [
            'summary' => $summary,
        ]);
    }
}