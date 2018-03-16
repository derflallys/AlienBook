<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15/03/18
 * Time: 19:38
 */

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Alien
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class Alien extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min="15")
     */
    private $age;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $family;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $race;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $food;
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Alien")
     * @ORM\JoinTable(name="alien_friends",
     *     joinColumns={@ORM\JoinColumn(name="alien_id",referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="friend_id",referencedColumnName="id")}
     * )
     */
    private $friends ;




    public function __construct()
    {
        parent::__construct();
        $this->friends = new ArrayCollection();

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @param mixed $family
     */
    public function setFamily($family)
    {
        $this->family = $family;
    }

    /**
     * @return mixed
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * @param mixed $race
     */
    public function setRace($race)
    {
        $this->race = $race;
    }

    /**
     * @return mixed
     */
    public function getFood()
    {
        return $this->food;
    }

    /**
     * @param mixed $food
     */
    public function setFood($food)
    {
        $this->food = $food;
    }

    /**
     * @return ArrayCollection
     */
    public function getFriends()
    {
        return $this->friends;
    }



    /**
     * @param Alien $friend
     */
    public function addFriends(Alien $friend)
    {
        if(!$this->getFriends()->contains($friend))
        {
            $this->friends [] = $friend;
            $friend->addFriends($this);
        }

    }

    public function removeFriend(Alien $friend)
    {
        if($this->getFriends()->contains($friend)) {
            $this->getFriends()->removeElement($friend);
            $friend->removeFriend($this);
        }

    }



}