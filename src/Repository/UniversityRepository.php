<?php

namespace App\Repository;

use App\Entity\University;
use App\Enum\EducationThreshold;
use App\Service\QueryHelperService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<University>
 *
 * @method University|null find($id, $lockMode = null, $lockVersion = null)
 * @method University|null findOneBy(array $criteria, array $orderBy = null)
 * @method University[]    findAll()
 * @method University[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UniversityRepository extends ServiceEntityRepository
{
    private string $prefix = 'university';

    public function __construct(ManagerRegistry $registry, private readonly QueryHelperService $queryHelperService)
    {
        parent::__construct($registry, University::class);
    }

    public function add(University $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(University $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getStatisticData($university, $dateStart, $dateEnd, $flow, $stage): array
    {
        if (empty($university)) {
            return [];
        }
        $ids = explode(',', $university);

        $qb = $this->createQueryBuilder($this->prefix)
            ->leftJoin($this->prefix.'.student', 'student', 'WITH', $this->prefix.'.id = student.university and student.status = \'Активен\'')->addSelect('student')
            ->leftJoin('student.education', 'education')->addSelect('education')
            ->where($this->prefix.'.id IN (:ids)')
            ->setParameters([
                'ids' => $ids,
            ]);

        if ($stage) {
            $qb->andWhere('education.stage = :stage')
                ->setParameter('stage', $stage);
        }
        if ($flow) {
            $qb->andWhere('education.flow = :flow')
                ->setParameter('flow', $flow);
        }

        $result = $qb->getQuery()->getArrayResult();

        foreach ($result as $key => $item) {
            $studentsCompleteAssessment = [];
            foreach ($item['student'] as $key2 => $student) {
                $grouperData = []; // Группируем данные по этапам ассесмента
                foreach ($student['education'] as $education) {
                    $stage = $education['stage'];
                    if (!isset($grouperData[$stage])) {
                        $grouperData[$stage] = [];
                    }
                    $grouperData[$stage][] = $education;
                }

                $medians = [];
                foreach ($grouperData as $stage) {
                    $stageResult = []; // Считаем медианы каждой группы ассесмента
                    foreach ($stage as $education) {
                        if ('-' == $education['result']) {
                            $stageResult[] = 0;
                        } else {
                            $stageResult[] = (float) $education['result'];
                        }
                    }
                    $medians[] = $this->queryHelperService->findMedian($stageResult);
                }

                foreach ($medians as $stage => $median) {
                    if ($median < EducationThreshold::MINIMUM->asFloat()) {
                        $result[$key]['student'][$key2]['assessment_result'][$stage] = false;
                    } else {
                        $result[$key]['student'][$key2]['assessment_result'][$stage] = true;
                        $studentsCompleteAssessment[$stage][] = 1;
                    }
                }
                $result[$key]['student'][$key2]['education'] = $grouperData;
            }
            $result[$key]['studentCompleteAssessment'] = $studentsCompleteAssessment;
        }

        return $result;
    }
}
