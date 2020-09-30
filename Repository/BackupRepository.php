<?php
/*
 * @author blutze-media
 * @since 2020-09-25
 */

namespace Enhavo\Bundle\BackupBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Enhavo\Bundle\AppBundle\Repository\EntityRepository;

class BackupRepository extends EntityRepository
{
    public function findBetween($property, \DateTime $from, \DateTime $to, $criteria = [], $orderBy = [])
    {
        /** @var QueryBuilder $query */
        $query = $this->createQueryBuilder('e');

        $query->where(sprintf('e.%s BETWEEN :from AND :to', $property))
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $i = 0;
        foreach($criteria  as $property => $value) {
            $query->andWhere(sprintf('e.%s = :criteria%s', $property, $i));
            $query->setParameter(sprintf('criteria%s', $i), $value);
            $i++;
        }

        foreach($orderBy as $property => $value) {
            $query->addOrderBy($property, $value);
        }

        return $query->getQuery()->getResult();
    }
}
