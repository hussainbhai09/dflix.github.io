<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubscriptionRepository")
 */
class Subscription
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
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;


    /**
     * @var string
     * @ORM\Column(name="method", type="string", length=255)
     */
    private $method;

    /**
     * @var string
     * @ORM\Column(name="pack", type="string", length=255)
     */
    private $pack;

    /**
     * @Assert\Length(
     *      min = 10,
     * )
     * @ORM\Column(name="infos", type="text" ,nullable=true)
     */
    private $infos;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;

    /**
     * @var string
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(name="transaction", type="string", length=255,nullable=true)
     */
    private $transaction;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255,nullable=true)
     */
    private $email;
    


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started", type="datetime",nullable=true)
     */
    private $started;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expired", type="datetime",nullable=true)
     */
    private $expired;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;


    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png","application/pdf" },maxSize="40M")
     */
    private $file;
     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;


    public function __construct()
    {
        $this->created= new \DateTime();

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
     * Set created
     *
     * @param \DateTime $created
     * @return Subscription
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
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
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
 
    /**
    * Get method
    * @return  
    */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
    * Set method
    * @return $this
    */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
    * Get pack
    * @return  
    */
    public function getPack()
    {
        return $this->pack;
    }
    
    /**
    * Set pack
    * @return $this
    */
    public function setPack($pack)
    {
        $this->pack = $pack;
        return $this;
    }

    /**
    * Get status
    * @return  
    */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
    * Set status
    * @return $this
    */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
    * Get currency
    * @return  
    */
    public function getCurrency()
    {
        return $this->currency;
    }
    
    /**
    * Set currency
    * @return $this
    */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
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
    * Get transaction
    * @return  
    */
    public function getTransaction()
    {
        return $this->transaction;
    }
    
    /**
    * Set transaction
    * @return $this
    */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
    * Get email
    * @return  
    */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
    * Set email
    * @return $this
    */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }


     /**
     * Get started
     * @return  
     */
     public function getStarted()
     {
         return $this->started;
     }
     
     /**
     * Set started
     * @return $this
     */
     public function setStarted($started)
     {
         $this->started = $started;
         return $this;
     }

     /**
     * Get expired
     * @return  
     */
     public function getExpired()
     {
         return $this->expired;
     }
     
     /**
     * Set expired
     * @return $this
     */
     public function setExpired($expired)
     {
         $this->expired = $expired;
         return $this;
     }

     /**
     * Get media
     * @return  
     */
     public function getMedia()
     {
         return $this->media;
     }
     
     /**
     * Set media
     * @return $this
     */
     public function setMedia($media)
     {
         $this->media = $media;
         return $this;
     }

     /**
     * Get file
     * @return  
     */
     public function getFile()
     {
         return $this->file;
     }
     
     /**
     * Set file
     * @return $this
     */
     public function setFile($file)
     {
         $this->file = $file;
         return $this;
     }

     /**
     * Get infos
     * @return  
     */
     public function getInfos()
     {
         return $this->infos;
     }
     
     /**
     * Set infos
     * @return $this
     */
     public function setInfos($infos)
     {
         $this->infos = $infos;
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
