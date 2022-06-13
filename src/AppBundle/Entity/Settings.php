<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MediaBundle\Entity\Media;
/**
 * Settings
 *
 * @ORM\Table(name="settings_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
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
     *
     * @ORM\Column(name="appname", type="string", length=255 , nullable = true)
     */
    private $appname;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255 , nullable = true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=255 , nullable = true)
     */
    private $subtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="sitedescription", type="string", length=255 , nullable = true)
     */
    private $sitedescription;

    /**
     * @var string
     *
     * @ORM\Column(name="sitekeywords", type="string", length=255 , nullable = true)
     */
    private $sitekeywords;

    /**
     * @var bool
     *
     * @ORM\Column(name="login", type="boolean")
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255 , nullable = true)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="cashaccount", type="text", nullable = true)
     */
    private $cashaccount;

    /**
     * @var string
     *
     * @ORM\Column(name="paypalclientid", type="string", length=255 , nullable = true)
     */
    private $paypalclientid;


    /**
     * @var string
     *
     * @ORM\Column(name="paypalclientsecret", type="string", length=255 , nullable = true)
     */
    private $paypalclientsecret;


    /**
     * @var string
     *
     * @ORM\Column(name="paypalaccount", type="string", length=255 , nullable = true)
     */
    private $paypalaccount;

     /**
     * @var string
     *
     * @ORM\Column(name="stripeapikey", type="text", nullable = true)
     */
    private $stripeapikey;

    /**
     * @var bool
     *
     * @ORM\Column(name="manual", type="boolean")
     */
    private $manual;
    /**
     * @var bool
     *
     * @ORM\Column(name="stripe", type="boolean")
     */
    private $stripe;

    /**
     * @var bool
     *
     * @ORM\Column(name="paypal", type="boolean")
     */
    private $paypal;

    /**
     * @var bool
     *
     * @ORM\Column(name="gpay", type="boolean")
     */
    private $gpay;

    /**
     * @var string
     *
     * @ORM\Column(name="stripepublickey", type="text", nullable = true)
     */
    private $stripepublickey;


     /**
     * @var boolean
     *
     * @ORM\Column(name="paypalsandbox", type="boolean")
     */
    private $paypalsandbox;


    /**
     * @var string
     *
     * @ORM\Column(name="appdescription", type="text", nullable = true)
     */
    private $appdescription;

    /**
     * @var string
     *
     * @ORM\Column(name="googleplay", type="text", nullable = true)
     */
    private $googleplay;

    /**
     * @var string
     *
     * @ORM\Column(name="privacypolicy", type="text", nullable = true)
     */
    private $privacypolicy;

    /**
     * @var string
     *
     * @ORM\Column(name="refundpolicy", type="text", nullable = true)
     */
    private $refundpolicy;

        /**
     * @var string
     *
     * @ORM\Column(name="faq", type="text", nullable = true)
     */
    private $faq;

    /**
     * @var string
     *
     * @ORM\Column(name="firebasekey", type="string", length=255 , nullable = true)
     */
    private $firebasekey;

    /**
     * @var string
     *
     * @ORM\Column(name="rewardedadmobid", type="string", length=255 , nullable = true)
     */
    private $rewardedadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="banneradmobid", type="string", length=255 , nullable = true)
     */
    private $banneradmobid;


    /**
     * @var string
     *
     * @ORM\Column(name="bannerfacebookid", type="string", length=255 , nullable = true)
     */
    private $bannerfacebookid;


    /**
     * @var string
     *
     * @ORM\Column(name="bannertype", type="string", length=255 , nullable = true)
     */
    private $bannertype;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeadmobid", type="string", length=255 , nullable = true)
     */
    private $nativeadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="nativefacebookid", type="string", length=255 , nullable = true)
     */
    private $nativefacebookid;

    /**
     * @var string
     *
     * @ORM\Column(name="nativeitem",  type="integer",  length=255 , nullable = true)
     */
    private $nativeitem;


    /**
     * @var string
     *
     * @ORM\Column(name="nativetype", type="string", length=255 , nullable = true)
     */
    private $nativetype;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialadmobid", type="string", length=255 , nullable = true)
     */
    private $interstitialadmobid;

    /**
     * @var string
     *
     * @ORM\Column(name="interstitialfacebookid", type="string", length=255 , nullable = true)
     */
    private $interstitialfacebookid;


     /**
     * @var string
     *
     * @ORM\Column(name="interstitialtype", type="string", length=255 , nullable = true)
     */
    private $interstitialtype;

     /**
     * @var string
     *
     * @ORM\Column(name="interstitialclick", type="integer", length=255 , nullable = true)
     */
    private $interstitialclick;

     /**
     * @var string
     *
     * @ORM\Column(name="homebanner", type="string", length=255 , nullable = true)
     */
    private $homebanner;

     /**
     * @var string
     *
     * @ORM\Column(name="homebannertype", type="string", length=255 , nullable = true)
     */
    private $homebannertype;


     /**
     * @var string
     *
     * @ORM\Column(name="moviebanner", type="string", length=255 , nullable = true)
     */
    private $moviebanner;

     /**
     * @var string
     *
     * @ORM\Column(name="moviebannertype", type="string", length=255 , nullable = true)
     */
    private $moviebannertype;

     /**
     * @var string
     *
     * @ORM\Column(name="seriebanner", type="string", length=255 , nullable = true)
     */
    private $seriebanner;

     /**
     * @var string
     *
     * @ORM\Column(name="seriebannertype", type="string", length=255 , nullable = true)
     */
    private $seriebannertype;

     /**
     * @var string
     *
     * @ORM\Column(name="channelbanner", type="string", length=255 , nullable = true)
     */
    private $channelbanner;

     /**
     * @var string
     *
     * @ORM\Column(name="channelbannertype", type="string", length=255 , nullable = true)
     */
    private $channelbannertype;


     /**
     * @var string
     *
     * @ORM\Column(name="themoviedbkey", type="string", length=255 , nullable = true)
     */
    private $themoviedbkey;


     /**
     * @var string
     *
     * @ORM\Column(name="themoviedblang", type="string", length=255 , nullable = true)
     */
    private $themoviedblang;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="text", nullable = true)
     */
    private $header;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $file;

    /**
     * @Assert\File(mimeTypes={"image/jpeg","image/png" },maxSize="40M")
     */
    private $favfile;
     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="logo_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $logo;

    

     /**
     * @ORM\ManyToOne(targetEntity="MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="favicon_id", referencedColumnName="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $favicon;


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
     * Set appname
     *
     * @param string $appname
     * @return Settings
     */
    public function setAppname($appname)
    {
        $this->appname = $appname;

        return $this;
    }

    /**
     * Get appname
     *
     * @return string 
     */
    public function getAppname()
    {
        return $this->appname;
    }

    /**
     * Set appdescription
     *
     * @param string $appdescription
     * @return Settings
     */
    public function setAppdescription($appdescription)
    {
        $this->appdescription = $appdescription;

        return $this;
    }

    /**
     * Get appdescription
     *
     * @return string 
     */
    public function getAppdescription()
    {
        return $this->appdescription;
    }

    /**
     * Set googleplay
     *
     * @param string $googleplay
     * @return Settings
     */
    public function setGoogleplay($googleplay)
    {
        $this->googleplay = $googleplay;

        return $this;
    }

    /**
     * Get googleplay
     *
     * @return string 
     */
    public function getGoogleplay()
    {
        return $this->googleplay;
    }

    /**
     * Set privacypolicy
     *
     * @param string $privacypolicy
     * @return Settings
     */
    public function setPrivacypolicy($privacypolicy)
    {
        $this->privacypolicy = $privacypolicy;

        return $this;
    }

    /**
     * Get privacypolicy
     *
     * @return string 
     */
    public function getPrivacypolicy()
    {
        return $this->privacypolicy;
    }

    /**
     * Set firebasekey
     *
     * @param string $firebasekey
     * @return Settings
     */
    public function setFirebasekey($firebasekey)
    {
        $this->firebasekey = $firebasekey;

        return $this;
    }

    /**
     * Get firebasekey
     *
     * @return string 
     */
    public function getFirebasekey()
    {
        return $this->firebasekey;
    }

    public function getFile()
    {
        return $this->file;
    }
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
    /**
     * Set media
     *
     * @param string $media
     * @return image
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return string 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
    * Get banneradmobid
    * @return  
    */
    public function getBanneradmobid()
    {
        return $this->banneradmobid;
    }
    
    /**
    * Set banneradmobid
    * @return $this
    */
    public function setBanneradmobid($banneradmobid)
    {
        $this->banneradmobid = $banneradmobid;
        return $this;
    }

    /**
    * Get bannerfacebookid
    * @return  
    */
    public function getBannerfacebookid()
    {
        return $this->bannerfacebookid;
    }
    
    /**
    * Set bannerfacebookid
    * @return $this
    */
    public function setBannerfacebookid($bannerfacebookid)
    {
        $this->bannerfacebookid = $bannerfacebookid;
        return $this;
    }

    /**
    * Get nativefacebookid
    * @return  
    */
    public function getNativefacebookid()
    {
        return $this->nativefacebookid;
    }
    
    /**
    * Set nativefacebookid
    * @return $this
    */
    public function setNativefacebookid($nativefacebookid)
    {
        $this->nativefacebookid = $nativefacebookid;
        return $this;
    }

    /**
    * Get nativeadmobid
    * @return  
    */
    public function getNativeadmobid()
    {
        return $this->nativeadmobid;
    }
    
    /**
    * Set nativeadmobid
    * @return $this
    */
    public function setNativeadmobid($nativeadmobid)
    {
        $this->nativeadmobid = $nativeadmobid;
        return $this;
    }

    /**
    * Get interstitialfacebookid
    * @return  
    */
    public function getInterstitialfacebookid()
    {
        return $this->interstitialfacebookid;
    }
    
    /**
    * Set interstitialfacebookid
    * @return $this
    */
    public function setInterstitialfacebookid($interstitialfacebookid)
    {
        $this->interstitialfacebookid = $interstitialfacebookid;
        return $this;
    }

    /**
    * Get interstitialadmobid
    * @return  
    */
    public function getInterstitialadmobid()
    {
        return $this->interstitialadmobid;
    }
    
    /**
    * Set interstitialadmobid
    * @return $this
    */
    public function setInterstitialadmobid($interstitialadmobid)
    {
        $this->interstitialadmobid = $interstitialadmobid;
        return $this;
    }

    /**
    * Get bannertype
    * @return  
    */
    public function getBannertype()
    {
        return $this->bannertype;
    }
    
    /**
    * Set bannertype
    * @return $this
    */
    public function setBannertype($bannertype)
    {
        $this->bannertype = $bannertype;
        return $this;
    }

    /**
    * Get interstitialtype
    * @return  
    */
    public function getInterstitialtype()
    {
        return $this->interstitialtype;
    }
    
    /**
    * Set interstitialtype
    * @return $this
    */
    public function setInterstitialtype($interstitialtype)
    {
        $this->interstitialtype = $interstitialtype;
        return $this;
    }

    /**
    * Get nativetype
    * @return  
    */
    public function getNativetype()
    {
        return $this->nativetype;
    }
    
    /**
    * Set nativetype
    * @return $this
    */
    public function setNativetype($nativetype)
    {
        $this->nativetype = $nativetype;
        return $this;
    }

    /**
    * Get interstitialclick
    * @return  
    */
    public function getInterstitialclick()
    {
        return $this->interstitialclick;
    }
    
    /**
    * Set interstitialclick
    * @return $this
    */
    public function setInterstitialclick($interstitialclick)
    {
        $this->interstitialclick = $interstitialclick;
        return $this;
    }

    /**
    * Get nativeitem
    * @return  
    */
    public function getNativeitem()
    {
        return $this->nativeitem;
    }
    
    /**
    * Set nativeitem
    * @return $this
    */
    public function setNativeitem($nativeitem)
    {
        $this->nativeitem = $nativeitem;
        return $this;
    }

    /**
    * Get rewardedadmobid
    * @return  
    */
    public function getRewardedadmobid()
    {
        return $this->rewardedadmobid;
    }
    
    /**
    * Set rewardedadmobid
    * @return $this
    */
    public function setRewardedadmobid($rewardedadmobid)
    {
        $this->rewardedadmobid = $rewardedadmobid;
        return $this;
    }

    /**
    * Get paypalaccount
    * @return  
    */
    public function getPaypalaccount()
    {
        return $this->paypalaccount;
    }
    
    /**
    * Set paypalaccount
    * @return $this
    */
    public function setPaypalaccount($paypalaccount)
    {
        $this->paypalaccount = $paypalaccount;
        return $this;
    }

    /**
    * Get stripeapikey
    * @return  
    */
    public function getStripeapikey()
    {
        return $this->stripeapikey;
    }
    
    /**
    * Set stripeapikey
    * @return $this
    */
    public function setStripeapikey($stripeapikey)
    {
        $this->stripeapikey = $stripeapikey;
        return $this;
    }

    /**
    * Get paypalsandbox
    * @return  
    */
    public function getPaypalsandbox()
    {
        return $this->paypalsandbox;
    }
    
    /**
    * Set paypalsandbox
    * @return $this
    */
    public function setPaypalsandbox($paypalsandbox)
    {
        $this->paypalsandbox = $paypalsandbox;
        return $this;
    }

    /**
    * Get stripepublickey
    * @return  
    */
    public function getStripepublickey()
    {
        return $this->stripepublickey;
    }
    
    /**
    * Set stripepublickey
    * @return $this
    */
    public function setStripepublickey($stripepublickey)
    {
        $this->stripepublickey = $stripepublickey;
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
    * Get cashaccount
    * @return  
    */
    public function getCashaccount()
    {
        return $this->cashaccount;
    }
    
    /**
    * Set cashaccount
    * @return $this
    */
    public function setCashaccount($cashaccount)
    {
        $this->cashaccount = $cashaccount;
        return $this;
    }

    /**
    * Get paypal
    * @return  
    */
    public function getPaypal()
    {
        return $this->paypal;
    }
    
    /**
    * Set paypal
    * @return $this
    */
    public function setPaypal($paypal)
    {
        $this->paypal = $paypal;
        return $this;
    }

    /**
    * Get stripe
    * @return  
    */
    public function getStripe()
    {
        return $this->stripe;
    }
    
    /**
    * Set stripe
    * @return $this
    */
    public function setStripe($stripe)
    {
        $this->stripe = $stripe;
        return $this;
    }

    /**
    * Get manual
    * @return  
    */
    public function getManual()
    {
        return $this->manual;
    }
    
    /**
    * Set manual
    * @return $this
    */
    public function setManual($manual)
    {
        $this->manual = $manual;
        return $this;
    }

    /**
    * Get refundpolicy
    * @return  
    */
    public function getRefundpolicy()
    {
        return $this->refundpolicy;
    }
    
    /**
    * Set refundpolicy
    * @return $this
    */
    public function setRefundpolicy($refundpolicy)
    {
        $this->refundpolicy = $refundpolicy;
        return $this;
    }

    /**
    * Get faq
    * @return  
    */
    public function getFaq()
    {
        return $this->faq;
    }
    
    /**
    * Set faq
    * @return $this
    */
    public function setFaq($faq)
    {
        $this->faq = $faq;
        return $this;
    }

    /**
    * Get favicon
    * @return  
    */
    public function getFavicon()
    {
        return $this->favicon;
    }
    
    /**
    * Set favicon
    * @return $this
    */
    public function setFavicon($favicon)
    {
        $this->favicon = $favicon;
        return $this;
    }
    /**
    * Get logo
    * @return  
    */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
    * Set logo
    * @return $this
    */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }
    /**
    * Get title
    * @return  
    */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
    * Set title
    * @return $this
    */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
    * Get subtitle
    * @return  
    */
    public function getSubtitle()
    {
        return $this->subtitle;
    }
    
    /**
    * Set subtitle
    * @return $this
    */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
    * Get sitekeywords
    * @return  
    */
    public function getSitekeywords()
    {
        return $this->sitekeywords;
    }
    
    /**
    * Set sitekeywords
    * @return $this
    */
    public function setSitekeywords($sitekeywords)
    {
        $this->sitekeywords = $sitekeywords;
        return $this;
    }

    /**
    * Get sitedescription
    * @return  
    */
    public function getSitedescription()
    {
        return $this->sitedescription;
    }
    
    /**
    * Set sitedescription
    * @return $this
    */
    public function setSitedescription($sitedescription)
    {
        $this->sitedescription = $sitedescription;
        return $this;
    }

    /**
    * Get favfile
    * @return  
    */
    public function getFavfile()
    {
        return $this->favfile;
    }
    
    /**
    * Set favfile
    * @return $this
    */
    public function setFavfile($favfile)
    {
        $this->favfile = $favfile;
        return $this;
    }

    /**
    * Get themoviedbkey
    * @return  
    */
    public function getThemoviedbkey()
    {
        return $this->themoviedbkey;
    }
    
    /**
    * Set themoviedbkey
    * @return $this
    */
    public function setThemoviedbkey($themoviedbkey)
    {
        $this->themoviedbkey = $themoviedbkey;
        return $this;
    }


    /**
    * Get themoviedblang
    * @return  
    */
    public function getThemoviedblang()
    {
        return $this->themoviedblang;
    }
    
    /**
    * Set themoviedblang
    * @return $this
    */
    public function setThemoviedblang($themoviedblang)
    {
        $this->themoviedblang = $themoviedblang;
        return $this;
    }

    /**
    * Get header
    * @return  
    */
    public function getHeader()
    {
        return $this->header;
    }
    
    /**
    * Set header
    * @return $this
    */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
    * Get paypalclientsecret
    * @return  
    */
    public function getPaypalclientsecret()
    {
        return $this->paypalclientsecret;
    }
    
    /**
    * Set paypalclientsecret
    * @return $this
    */
    public function setPaypalclientsecret($paypalclientsecret)
    {
        $this->paypalclientsecret = $paypalclientsecret;
        return $this;
    }

    /**
    * Get paypalclientid
    * @return  
    */
    public function getPaypalclientid()
    {
        return $this->paypalclientid;
    }
    
    /**
    * Set paypalclientid
    * @return $this
    */
    public function setPaypalclientid($paypalclientid)
    {
        $this->paypalclientid = $paypalclientid;
        return $this;
    }

    /**
    * Get gpay
    * @return  
    */
    public function getGpay()
    {
        return $this->gpay;
    }
    
    /**
    * Set gpay
    * @return $this
    */
    public function setGpay($gpay)
    {
        $this->gpay = $gpay;
        return $this;
    }

    /**
    * Get login
    * @return  
    */
    public function getLogin()
    {
        return $this->login;
    }
    
    /**
    * Set login
    * @return $this
    */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
    * Get homebannertype
    * @return  
    */
    public function getHomebannertype()
    {
        return $this->homebannertype;
    }
    
    /**
    * Set homebannertype
    * @return $this
    */
    public function setHomebannertype($homebannertype)
    {
        $this->homebannertype = $homebannertype;
        return $this;
    }

    /**
    * Get homebanner
    * @return  
    */
    public function getHomebanner()
    {
        return $this->homebanner;
    }
    
    /**
    * Set homebanner
    * @return $this
    */
    public function setHomebanner($homebanner)
    {
        $this->homebanner = $homebanner;
        return $this;
    }

    /**
    * Get moviebanner
    * @return  
    */
    public function getMoviebanner()
    {
        return $this->moviebanner;
    }
    
    /**
    * Set moviebanner
    * @return $this
    */
    public function setMoviebanner($moviebanner)
    {
        $this->moviebanner = $moviebanner;
        return $this;
    }
    /**
    * Get moviebannertype
    * @return  
    */
    public function getMoviebannertype()
    {
        return $this->moviebannertype;
    }
    
    /**
    * Set moviebannertype
    * @return $this
    */
    public function setMoviebannertype($moviebannertype)
    {
        $this->moviebannertype = $moviebannertype;
        return $this;
    }

    /**
    * Get seriebanner
    * @return  
    */
    public function getSeriebanner()
    {
        return $this->seriebanner;
    }
    
    /**
    * Set seriebanner
    * @return $this
    */
    public function setSeriebanner($seriebanner)
    {
        $this->seriebanner = $seriebanner;
        return $this;
    }

    /**
    * Get seriebannertype
    * @return  
    */
    public function getSeriebannertype()
    {
        return $this->seriebannertype;
    }
    
    /**
    * Set seriebannertype
    * @return $this
    */
    public function setSeriebannertype($seriebannertype)
    {
        $this->seriebannertype = $seriebannertype;
        return $this;
    }

    /**
    * Get channelbanner
    * @return  
    */
    public function getChannelbanner()
    {
        return $this->channelbanner;
    }
    
    /**
    * Set channelbanner
    * @return $this
    */
    public function setChannelbanner($channelbanner)
    {
        $this->channelbanner = $channelbanner;
        return $this;
    }

    /**
    * Get channelbannertype
    * @return  
    */
    public function getChannelbannertype()
    {
        return $this->channelbannertype;
    }
    
    /**
    * Set channelbannertype
    * @return $this
    */
    public function setChannelbannertype($channelbannertype)
    {
        $this->channelbannertype = $channelbannertype;
        return $this;
    }
}
