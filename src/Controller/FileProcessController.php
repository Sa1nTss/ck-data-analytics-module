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

        return $this->render('file_process/index.html.twig', [
            'auth' => $auth,
            'controller' => $this->controller,
        ]);
    }

    #[Route('/upload_education_excel', name: 'upload_education_excel')]
    public function uploadEducationExcel(): Response
    {
        $result = $this->excelProcessor->processEducationExcel($this->request->files->get('file'));

        return $this->json($result);
    }

    #[Route('/upload_faculty_excel', name: 'upload_faculty_excel')]
    public function uploadFacultyExcel(): Response
    {
        $result = $this->excelProcessor->processFacultyExcel($this->request->files->get('file'));

        return $this->json($result);
    }
}
