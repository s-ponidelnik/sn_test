<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllHiddenAsArray(bool $showHidden): array
    {
        $result = $this->createQueryBuilder('u');
        if (!$showHidden) {
            $result->where('u.is_hide = :hide')->setParameter('hide', false);
        }
        return $result->getQuery()->getArrayResult();
    }

    public function findByIdAsArray(int $id): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult()[0];
    }

    public function findHiddenByLikeUsername(string $value, bool $showHidden): array
    {
        $qb = $this->createQueryBuilder('u');
        $result = $qb->where(
            $qb->expr()->like('u.username', ':user')
        )->setParameter('user', '%' . $value . '%');
        if ($showHidden == false) {
            $result = $result->andWhere('u.is_hide = 0');
        }
        $result = $result->getQuery()->getArrayResult();
        return array_combine(array_column($result, 'username'), $result);


    }
}
