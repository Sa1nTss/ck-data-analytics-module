<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\Student;
use App\Service\AuthService;
use App\Service\LinkService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalyticsController extends BaseController
{
    use AuthService;
    use LinkService;
    public string $controller = 'analytics';

    #[Route('/analytics/analytics', name: 'analytics')]
    public function index(): Response
    {
        $auth = $this->getAuthValue($this->getUser(), 'auth_main', $this->managerRegistry);
        if (!is_array($auth)) {
            return $auth;
        }

        $tpl = $this->request->get('ajax') ? 'analytics/analytics/table.html.twig' : 'analytics/analytics/index.html.twig';
        $result = $this->get();
        $result['auth'] = $auth;

        return $this->render($tpl,
            $result,
        );
    }

    public function get(): array
    {
        $filters = [
            'page' => $this->request->get('page') ?? null,
            'on_page' => $this->request->get('on_page') ?? 25,
            'sort' => !empty($this->request->get('sort')) ? $this->request->get('sort') : 'id',
            'filter' => $this->request->get('filter') ?? null,
        ];
        $search = $this->request->get('search');

        $result = $this->doctrine->getRepository(Education::class)->getList($filters, $search);

        $count = $this->doctrine->getRepository(Education::class)->getCount($filters);

        return [
            'data' => [],
            'students' => $this->doctrine->getRepository(Student::class)->findBy([], ['fio' => 'ASC']),
            'pager' => [
                'count_all_position' => $count,
                'current_page' => $filters['page'] ?? 1,
                'count_page' => (int) ceil($count / $filters['on_page']),
                'paginator_link' => $this->getParinatorLink(),
                'on_page' => $filters['on_page'],
            ],
            'sort' => [
                'sort_link' => $this->getSortLink(),
                'current_sort' => $this->request->get('sort') ?? null,
            ],
            'csv_link' => $this->getCSVLink(),
            'controller' => $this->controller,
            'table' => $this->setTable(),
        ];
    }

    #[Route('/analytics/table_data', name: 'analytics_table_data')]
    public function getAnalyticsTableData(): Response
    {
        $student = $this->request->get('student');

        $data = $this->doctrine->getRepository(Education::class)->findBy([
            'student' => $student,
        ]);

        return $this->render('analytics/analytics/part/table.html.twig', [
            'data' => $data,
        ]);
    }

    private function setTable(): array
    {
        return [
            [
                'name' => 'student',
                'header' => 'ID',
                'type' => 'join',
                'join' => 'inopolis_id',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],
            [
                'name' => 'student',
                'header' => 'ФИО',
                'type' => 'join',
                'join' => 'fio',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],
            [
                'name' => 'flow',
                'header' => 'Поток',
                'type' => 'string',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],
            [
                'name' => 'program',
                'header' => 'Программа',
                'type' => 'join',
                'join' => 'name',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],
            [
                'name' => 'direction',
                'header' => 'Направление',
                'type' => 'join',
                'join' => 'name',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],
            [
                'name' => 'competence',
                'header' => 'Компентенция',
                'type' => 'join',
                'join' => 'name',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],
            [
                'name' => 'student',
                'header' => 'Статус',
                'type' => 'join',
                'join' => 'status',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],
            /*[
                'name' => 'date',
                'header' => 'Дата начала прохождения оценки',
                'type' => 'date',
                'filter' => true,
                'show' => true,
                'sort' => true,
            ],*/
        ];
    }
}
