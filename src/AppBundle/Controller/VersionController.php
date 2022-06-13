<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Version;
use AppBundle\Form\VersionType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class VersionController extends Controller
{

    public function addAction(Request $request)
    {
        $version= new Version();
        $form = $this->createForm(VersionType::class,$version);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($version);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_version_index'));
        }
        return $this->render("AppBundle:Version:add.html.twig",array("form"=>$form->createView()));
    }

    public function api_checkAction(Request $request,$code,$user,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();
        $version =   $em->getRepository("AppBundle:Version")->findOneBy(array("code"=>$code,"enabled"=>true));
        $response=array();
        $code="200";
        $message="";
        $errors=array();
        if ($version==null) {
            $versions =  $em->getRepository("AppBundle:Version")->findBy(array("enabled"=>true),array("code"=>"asc"));
            $a=null;
            foreach ($versions as $key => $value) {
                $a=$value;
            }
            if ($a==null) {
                $code="200";
                $response["name"]="update";
                $response["value"]="App on update";
            }else{
                $code="202";
                $response["name"]="update";
                $response["value"]="New version available ".$a->getTitle() ." please update your application";
                $message = $a->getFeatures();
            }
        }else{
            $code="200";
            $response["name"]="update";
            $response["value"]="App on update";
        }
        $response_user["name"] = "user";
        $response_user["value"] = "200";
        $response_user_subscription["name"] = "subscription";
        $response_user_subscription["value"] = "FALSE";
        if ($user!=0) {
            $user_obj =   $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$user,"enabled"=>true));
            if ($user_obj==null) {
                $response_user["name"] = "user";
                $response_user["value"] = "403";
            }else{
                $response_user["name"] = "user";
                $response_user["value"] = "200";
                $response_user_subscription["name"] = "subscription";
                $response_user_subscription["value"] = ($user_obj->isSubscribed())?"TRUE":"FALSE";
            }
        }



        $errors[]=$response;
        $errors[]=$response_user;
        $errors[]=$response_user_subscription;


        $settings =   $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $response_ads_rewarded["name"] = "ADMIN_REWARDED_ADMOB_ID";
        $response_ads_rewarded["value"] = $settings->getRewardedadmobid();

        $response_ads_interstitial_admob_id["name"] = "ADMIN_INTERSTITIAL_ADMOB_ID";
        $response_ads_interstitial_admob_id["value"] = $settings->getInterstitialadmobid();
        
        $response_ads_interstitial_facebook_id["name"] = "ADMIN_INTERSTITIAL_FACEBOOK_ID";
        $response_ads_interstitial_facebook_id["value"] = $settings->getInterstitialfacebookid();


        $response_ads_interstitial_type["name"] = "ADMIN_INTERSTITIAL_TYPE";
        $response_ads_interstitial_type["value"] = $settings->getInterstitialtype();

        $response_ads_interstitial_click["name"] = "ADMIN_INTERSTITIAL_CLICKS";
        $response_ads_interstitial_click["value"] = $settings->getInterstitialclick();

        $response_ads_banner_admob_id["name"] = "ADMIN_BANNER_ADMOB_ID";
        $response_ads_banner_admob_id["value"] = $settings->getBanneradmobid();


        $response_ads_banner_facebook_id["name"] = "ADMIN_BANNER_FACEBOOK_ID";
        $response_ads_banner_facebook_id["value"] = $settings->getBannerfacebookid();

        $response_ads_banner_type["name"] = "ADMIN_BANNER_TYPE";
        $response_ads_banner_type["value"] = $settings->getBannertype();

        $response_ads_native_facebook_id["name"] = "ADMIN_NATIVE_FACEBOOK_ID";
        $response_ads_native_facebook_id["value"] = $settings->getNativefacebookid();

        $response_ads_native_admob_id["name"] = "ADMIN_NATIVE_ADMOB_ID";
        $response_ads_native_admob_id["value"] = $settings->getNativeadmobid();

        $response_ads_native_item["name"] = "ADMIN_NATIVE_LINES";
        $response_ads_native_item["value"] = $settings->getNativeitem();


        $response_ads_native_type["name"] = "ADMIN_NATIVE_TYPE";
        $response_ads_native_type["value"] = $settings->getNativetype();


        $response_currency["name"] = "APP_CURRENCY";
        $response_currency["value"] = $settings->getCurrency();


        $response_cash_account["name"] = "APP_CASH_ACCOUNT";
        $response_cash_account["value"] = $settings->getCashaccount();

        $response_stripe_public_key["name"] = "APP_STRIPE_PUBLIC_KEY";
        $response_stripe_public_key["value"] = $settings->getStripepublickey();


        $response_stripe_enabled["name"] = "APP_STRIPE_ENABLED";
        $response_stripe_enabled["value"] = ($settings->getStripe())? "TRUE":"FALSE";

        $response_paypal_enabled["name"] = "APP_PAYPAL_ENABLED";
        $response_paypal_enabled["value"] = ($settings->getPaypal())? "TRUE":"FALSE";



        $response_paypal_client_id["name"] = "APP_PAYPAL_CLIENT_ID";
        $response_paypal_client_id["value"] = $settings->getPaypalclientid();


        $response_cash_enabled["name"] = "APP_CASH_ENABLED";
        $response_cash_enabled["value"] = ($settings->getManual())? "TRUE":"FALSE";

        $response_gplay_enabled["name"] = "APP_GPLAY_ENABLED";
        $response_gplay_enabled["value"] = ($settings->getGpay())? "TRUE":"FALSE";


        $response_app_login_required["name"] = "APP_LOGIN_REQUIRED";
        $response_app_login_required["value"] = ($settings->getLogin())? "TRUE":"FALSE";


        $errors[]=$response_ads_rewarded;
        $errors[]=$response_ads_interstitial_admob_id;
        $errors[]=$response_ads_interstitial_facebook_id;
        $errors[]=$response_ads_interstitial_type;
        $errors[]=$response_ads_interstitial_click;
        $errors[]=$response_ads_banner_admob_id;
        $errors[]=$response_ads_banner_facebook_id;
        $errors[]=$response_ads_banner_type;
        $errors[]=$response_ads_native_facebook_id;
        $errors[]=$response_ads_native_admob_id;
        $errors[]=$response_ads_native_item;
        $errors[]=$response_ads_native_type;
        $errors[]=$response_currency;
        $errors[]=$response_cash_account;
        $errors[]=$response_stripe_public_key;
        $errors[]=$response_stripe_enabled;
        $errors[]=$response_paypal_enabled;
        $errors[]=$response_cash_enabled;
        $errors[]=$response_gplay_enabled;
        $errors[]=$response_app_login_required;
        $errors[]=$response_paypal_client_id;
        
        $error=array(
                "code"=>$code,
                "message"=>$message,
                "values"=>$errors,
                );
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);  
    }
    public function indexAction()
    {
	    $em=$this->getDoctrine()->getManager();
        $versions=$em->getRepository('AppBundle:Version')->findBy(array(),array("code"=>"asc"));
	    return $this->render('AppBundle:Version:index.html.twig',array("versions"=>$versions));    
	}
  

    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $version = $em->getRepository("AppBundle:Version")->find($id);
        if($version==null){
            throw new NotFoundHttpException("Page not found");
        }

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            
            //if (sizeof($version->getAlbums())==0) {
                $em->remove($version);
                $em->flush();


                $this->addFlash('success', 'Operation has been done successfully');
            //}else{
             //   $this->addFlash('danger', 'Operation has been cancelled ,Your album not empty');   
            //}
            return $this->redirect($this->generateUrl('app_version_index'));
        }
        return $this->render('AppBundle:Version:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $version=$em->getRepository("AppBundle:Version")->find($id);
        if ($version==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(VersionType::class,$version);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($version);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_version_index'));
 
        }
        return $this->render("AppBundle:Version:edit.html.twig",array("form"=>$form->createView()));
    }
}
?>