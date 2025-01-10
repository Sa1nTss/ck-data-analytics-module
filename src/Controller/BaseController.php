<?php

namespace App\Controller;

use App\Service\AuthService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    use AuthService;

    protected array $get_data;
    protected ManagerRegistry $managerRegistry;
    protected Request $request;
    protected ObjectManager $entityManager;

    public function __construct(
        protected ManagerRegistry $doctrine,
    ) {
        $this->request = Request::createFromGlobals();
        $this->managerRegistry = $this->doctrine;
        $this->get_data = $this->request->query->all();
        $this->entityManager = $this->managerRegistry->getManager();
    }
}
