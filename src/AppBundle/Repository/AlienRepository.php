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
            ->andWhere('friends != :me')
            ->setParameter('me',$alien)
          ->andWhere('alien != :al')
          ->setParameter('al',$alien)
            ->getQuery()
            ->execute();
    }
}