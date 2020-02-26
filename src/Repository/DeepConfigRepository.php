<?php

namespace App\Repository;

use App\Entity\DeepConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeepConfigRepository extends ServiceEntityRepository
{
    /**
     * DeepConfigRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeepConfig::class);
    }

    /**
     * @param string $key
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isKey(string $key)
    {
        $result = $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.param = :key')
            ->setParameter(':key', $key)
            ->getQuery()
            ->getSingleScalarResult();
        if ($result == 1) return true;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function updateByKey(string $key, $value = null)
    {
        $this->createQueryBuilder('c')
            ->update(DeepConfig::class, 'c')
            ->where('c.param = :key')
            ->set('c.value', ':value')
            ->setParameter('key', $key)
            ->setParameter('value', $value)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $key
     */
    public function deleteByKey(string $key, $value = null)
    {
        $this->createQueryBuilder('c')
            ->delete(DeepConfig::class, 'c')
            ->where('c.param = :key')
            ->setParameter('key', $key)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $key
     * @return DeepConfig|null
     */
    public function findOneByKey(string $key)
    {
        return $this->findOneBy([
            'param' => $key
        ]);
    }
}
