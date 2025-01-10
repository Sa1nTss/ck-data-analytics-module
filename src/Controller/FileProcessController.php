<?php

namespace App\Controller;

use App\Service\ExcelProcessor;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileProcessController extends BaseController
{
    public string $controller = 'file_process';
    public function __construct(ManagerRegistry $doctrine, private readonly ExcelProcessor $excelProcessor)
    {
        parent::__construct($doctrine);
    }

    #[Route('/file_process', name: 'file_process')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $auth = $this->getAuthValue($this->getUser(), 'auth_main', $this->managerRegistry);
        if (!is_array($auth)) {
            return $auth;
        }

        return $this->render('file_process/index.html.twig', compact([
            'auth',
            'controller' => $this->controller,
        ]));
    }

    #[Route('/upload_excel', name: 'upload_excel')]
    public function uploadExcel(): Response
    {
        $result = $this->excelProcessor->processExcel($this->request->files->get('file'));

        return $this->json($result);
    }
}
