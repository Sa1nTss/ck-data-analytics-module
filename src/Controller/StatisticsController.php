<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\Faculty;
use App\Entity\University;
use App\Service\AuthService;
use App\Service\LinkService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends BaseController
{
    use AuthService;
    use LinkService;
    public string $controller = 'statistics';

    #[Route('/analytics/statistics', name: 'statistics')]
    public function index(): Response
    {
        $auth = $this->getAuthValue($this->getUser(), 'auth_main', $this->managerRegistry);
        if (!is_array($auth)) {
            return $auth;
        }

        $tpl = $this->request->get('ajax') ? 'analytics/statistics/table.html.twig' : 'analytics/statistics/index.html.twig';
        $result = $this->get();
        $result['auth'] = $auth;

        return $this->render($tpl,
            $result,
        );
    }

    public function get(): array
    {
        return [
            'data' => [],
            'controller' => $this->controller,
            'faculties' => $this->managerRegistry->getRepository(Faculty::class)->findBy([], ['name' => 'ASC']),
            'universities' => $this->managerRegistry->getRepository(University::class)->findBy([], ['name' => 'ASC']),
        ];
    }

    #[Route('/analytics/statistics/get_table', name: 'statistics_get_table')]
    public function getStatisticTable(): Response
    {
        $university = $this->request->get('university');
        $faculty = $this->request->get('faculty');
        $dateStart = $this->request->get('date_start');
        $dateEnd = $this->request->get('date_end');
        $flow = $this->request->get('flow');
        $stage = $this->request->get('stage');

        $universityStatistic = $this->doctrine->getRepository(University::class)
            ->getStatisticData($university, $dateStart, $dateEnd, $flow, $stage);

        $facultyStatistic = $this->doctrine->getRepository(Faculty::class)
            ->getStatisticData($faculty, $dateStart, $dateEnd, $flow, $stage);

        return $this->render('analytics/statistics/part/table.html.twig', [
            'data' => [],
        ]);
    }
}
