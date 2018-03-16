<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16/03/18
 * Time: 15:36
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Alien;
use Doctrine\ORM\EntityRepository;

class AlienRepository extends  EntityRepository
{
    /**
     * @param Alien $alien
     * @return Alien[]
     */
    public function findFriends(Alien $alien)
    {

        return  $this->createQueryBuilder('alien')
            ->leftJoin('alien.friends','friends')
            ->andWhere('friends.id IS NULL OR friends.id<> :me')
            ->setParameter('me',$alien->getId())
            ->andWhere('alien.id != :me')
            ->getQuery()
            ->execute();
    }
}