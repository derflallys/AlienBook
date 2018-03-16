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
    public function findFriends(Alien $alien)
    {

    }
}