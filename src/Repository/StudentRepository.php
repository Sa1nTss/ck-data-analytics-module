<?php

namespace App\Repository;

use App\Entity\Student;
use App\Enum\EducationThreshold;
use App\Service\QueryHelperService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    private string $prefix = 'student';

    public function __construct(ManagerRegistry $registry, private readonly QueryHelperService $queryHelperService)
    {
        parent::__construct($registry, Student::class);
    }

    public function add(Student $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Student $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getEducationData($student): array
    {
        $qb = $this->createQueryBuilder($this->prefix)
            ->leftJoin($this->prefix.'.education', 'education')->addSelect('education')
            ->leftJoin('education.program', 'program')->addSelect('program')
            ->leftJoin('education.competence', 'competence')->addSelect('competence')
            ->leftJoin('education.direction', 'direction')->addSelect('direction')
            ->where('student = :student')
            ->setParameter('student', $student);

        $result = $qb->getQuery()->getArrayResult();

        foreach ($result as $key => $student) {
            $grouperData = [];
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
                    $result[$key]['assessment_result'][$stage] = false;
                } else {
                    $result[$key]['assessment_result'][$stage] = true;
                }
            }
        }

        return $result;
    }
}
