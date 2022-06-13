<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Category
 *
 * @ORM\Table(name="items_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ItemRepository")
 */
class Item
{
   
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="Poster")
     * @ORM\JoinColumn(name="poster_id", referencedColumnName="id", nullable=true)
     */
    private $poster;


     /**
     * @ORM\ManyToOne(targetEntity="Channel")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", nullable=true)
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    public function __construct()
    {

    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

   

    /**
     * Set position
     *
     * @param integer $position
     * @return Category
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
    /**
    * Get poster
    * @return  
    */
    public function getPoster()
    {
        return $this->poster;
    }
    
    /**
    * Set poster
    * @return $this
    */
    public function setPoster($poster)
    {
        $this->poster = $poster;
        return $this;
    }

    /**
    * Get channel
    * @return  
    */
    public function getChannel()
    {
        return $this->channel;
    }
    
    /**
    * Set channel
    * @return $this
    */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }
    /**
    * Get user
    * @return  
    */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
    * Set user
    * @return $this
    */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

   
}
