<?php

namespace App\Repository;

use App\Entity\Education;
use App\Service\QueryHelperService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Education>
 *
 * @method Education|null find($id, $lockMode = null, $lockVersion = null)
 * @method Education|null findOneBy(array $criteria, array $orderBy = null)
 * @method Education[]    findAll()
 * @method Education[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducationRepository extends ServiceEntityRepository
{
    private string $prefix = 'education';

    public function __construct(ManagerRegistry $registry, private readonly QueryHelperService $queryHelperService)
    {
        parent::__construct($registry, Education::class);
    }

    public function add(Education $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Education $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getList(array $filters, ?string $search = null): array
    {
        $qb = $this->createQueryBuilder($this->prefix);
        $this->queryHelperService->setListQuery(
            $qb,
            $filters,
            $this->prefix,
            $this->queryHelperService->getDefaultSort($filters['sort'] ?? null, $this->prefix),
        );

        $qb->leftJoin($this->prefix.'.student', 'student')->addSelect('student');
        $qb->leftJoin($this->prefix.'.competence', 'competence')->addSelect('competence');
        $qb->leftJoin($this->prefix.'.direction', 'direction')->addSelect('direction');
        $qb->leftJoin($this->prefix.'.program', 'program')->addSelect('program');

        return $qb->getQuery()->getArrayResult();
    }

    public function getCount(array $filters): int
    {
        $qb = $this->createQueryBuilder($this->prefix);
        $this->queryHelperService->setCountQuery($qb, $this->prefix);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
