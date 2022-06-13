<?php
// src/AppBundle/Entity/User.php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use MediaBundle\Entity\Media as Media;
use AppBundle\Entity\Comment as Comment;
use AppBundle\Entity\Rate as Rate;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_table")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /** 
    @ORM\Column(name="name", type="string", length=255, nullable=true) 
    */
    protected $name; 


    /** 
    @ORM\Column(name="theme", type="string", length=255, nullable=true) 
    */
    protected $theme; 

    /** 
    @ORM\Column(name="type", type="string", length=255, nullable=true) 
    */
    protected $type; 


    /** 
    @ORM\Column(name="locked", type="boolean") 
    */
    protected $locked; 

    /** 
    @ORM\Column(name="expired", type="boolean") 
    */
    protected $expired; 


    /** 
    @ORM\Column(name="credentials_expired", type="boolean") 
    */
    protected $credentialsExpired; 
    
    /** 
    @ORM\Column(name="token", type="text", nullable=true) 
    */
    protected $token; 

    /**
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user",cascade={"persist", "remove"})
    * @ORM\OrderBy({"created" = "desc"})
    */
    private $comments;

    /**
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Subscription", mappedBy="user",cascade={"persist", "remove"})
    * @ORM\OrderBy({"created" = "desc"})
    */
    private $subscriptions;

    /**
    * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rate", mappedBy="user",cascade={"persist", "remove"})
    * @ORM\OrderBy({"created" = "desc"})
    */
    private $ratings;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media" ,cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=true)
     */
    private $media;


    protected $privacypolicy; 

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $file;
  
    public function __construct()
    {
        parent::__construct();
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->roles = array();
        $this->credentialsExpired = false;
    }
       
     /**
    * Get type
    * @return  
    */

    public function getType()
    {
        return $this->type;
    }
    public function  setAutoSalt(){
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
    /**
    * Set type
    * @return $this
    */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /**
    * Get name
    * @return  
    */
    public function getName()
    {
        return $this->name;
    }
    
    /**
    * Set name
    * @return $this
    */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail($email) 
    {
        $this->email = $email;
        $this->username = $email;
    }
    public function __toString()
    {
       return $this->getName();
    }



    /**
    * Get token
    * @return  
    */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
    * Set token
    * @return $this
    */
    public function setToken($token)
    {
        $this->token = $token;
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
    public function setMedia(Media $media)
    {
        $this->media = $media;
        return $this;
    }
    /**
    * Get privacypolicy
    * @return  
    */
    public function getPrivacypolicy()
    {
        return $this->privacypolicy;
    }
    
    /**
    * Set privacypolicy
    * @return $this
    */
    public function setPrivacypolicy($privacypolicy)
    {
        $this->privacypolicy = $privacypolicy;
        return $this;
    }

    /**
    * Get theme
    * @return  
    */
    public function getTheme()
    {
        return $this->theme;
    }
    
    /**
    * Set theme
    * @return $this
    */
    public function setTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }
    /**
    * Get subscriptions
    * @return  
    */
    public function isSubscribed()
    {
        $subscribed  = false;
        if ($this->getSubscriptions()!=null) {
            foreach ($this->getSubscriptions() as $key => $subscription) {
                if ($subscription->getExpired()!=null && $subscription->getStatus() == "paid") {
                    if ($subscription->getExpired() !=null) {                   
                        if ($subscription->getExpired() > new \DateTime() )  {
                            $subscribed = true;
                        }
                    }
                }
            }
         }
        return $subscribed;
    }
    /**
    * Get subscriptions
    * @return  
    */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }
    
    /**
    * Set subscriptions
    * @return $this
    */
    public function setSubscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;
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
}