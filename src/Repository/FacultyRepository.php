<?php

namespace App\Repository;

use App\Entity\Faculty;
use App\Enum\EducationThreshold;
use App\Service\QueryHelperService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faculty>
 *
 * @method Faculty|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faculty|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faculty[]    findAll()
 * @method Faculty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacultyRepository extends ServiceEntityRepository
{
    private string $prefix = 'faculty';
    public function __construct(ManagerRegistry $registry, private readonly QueryHelperService $queryHelperService)
    {
        parent::__construct($registry, Faculty::class);
    }

    public function add(Faculty $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Faculty $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getStatisticData($faculty, $dateStart, $dateEnd, $flow, $stage): array
    {
        if (empty($faculty)) {
            return [];
        }
        $ids = explode(',', $faculty);

        $qb = $this->createQueryBuilder($this->prefix)
            ->leftJoin($this->prefix.'.student', 'student', 'WITH', $this->prefix.'.id = student.faculty and student.status = \'Активен\'')->addSelect('student')
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
