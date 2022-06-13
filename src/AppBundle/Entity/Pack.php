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
 * @ORM\Table(name="pack_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PackRepository")
 */
class Pack
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
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     * )
     * @ORM\Column(name="title", type="string", length=255))
     */
    private $title;

     /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255,nullable = true)
     */
    private $description;


     /**
     * @var string
     *
     * @ORM\Column(name="discount", type="string", length=255,nullable = true)
     */
    private $discount;

     /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;


     /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;


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
     * Set title
     *
     * @param string $title
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }





    
    public function __toString()
    {
        return $this->title;
    }

    /**
    * Get description
    * @return  
    */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
    * Set description
    * @return $this
    */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
    * Get price
    * @return  
    */
    public function getPrice()
    {
        return $this->price;
    }
    
    /**
    * Set price
    * @return $this
    */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
    * Get discount
    * @return  
    */
    public function getDiscount()
    {
        return $this->discount;
    }
    
    /**
    * Set discount
    * @return $this
    */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

 	/**
 	* Get duration
 	* @return  
 	*/
 	public function getDuration()
 	{
 	    return $this->duration;
 	}
 	
 	/**
 	* Set duration
 	* @return $this
 	*/
 	public function setDuration($duration)
 	{
 	    $this->duration = $duration;
 	    return $this;
 	}
}
