<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MediaBundle\Entity\Media;
use AppBundle\Entity\Subscription;
use AppBundle\Form\SubscriptionType;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use PayPal\Api\Payment;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
class SubscriptionController extends Controller
{
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $q = " ";
        if ($request->query->has("status") and $request->query->get("status") != "all") {
            $q .= " AND  p.status like '" . $request->query->get("status") . "'";
        }

        $dql = "SELECT p FROM AppBundle:Subscription p  WHERE 1 = 1 " . $q . " ORDER BY p.created desc ";
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $subscriptions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );
        $subscription_count= $em->getRepository("AppBundle:Subscription")->count();

        return $this->render('AppBundle:Subscription:index.html.twig', array("subscription_count"=>$subscription_count,"subscriptions" => $subscriptions));
    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $subscription = $em->getRepository("AppBundle:Subscription")->find($id);
        if($subscription==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {;
            $media_old = $subscription->getMedia();
            $em->remove($subscription);
            $em->flush();
            if( $media_old!=null ){
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_subscription_index'));
        }
        return $this->render('AppBundle:Subscription:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $subscription=$em->getRepository("AppBundle:Subscription")->find($id);
        if ($subscription==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(SubscriptionType::class,$subscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_subscription_index'));
 
        }
        return $this->render("AppBundle:Subscription:edit.html.twig",array("subscription"=>$subscription,"form"=>$form->createView()));
    }
    public function api_intentAction(Request $request,$token){

        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();

        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());

           
        $user=$request->get("user");
        $key=$request->get("key");
        $plan_id=$request->get("plan");


        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $user_obj=$em->getRepository("UserBundle:User")->find($user);
        $pack=$em->getRepository("AppBundle:Pack")->find($plan_id);
        $code = 500;
        $message = "";
        $values= array();
        if ($user_obj != null and $pack != null) {
            if (sha1($user_obj->getPassword()) == $key and $user_obj->isEnabled()) {

                \Stripe\Stripe::setApiKey($settings->getStripeapikey());
                $intent =  \Stripe\PaymentIntent::create([
                  'amount' => $pack->getPrice()*100,
                  'currency' => strtolower($settings->getCurrency()),
                  'payment_method_types' => ['card'],
                    'metadata' => [
                        'user_id' => $user_obj->getId(),
                        "plan_id" => $pack->getId()
                    ],
                ]);
                $values[]=array("name"=>"client_secret","value"=>$intent->client_secret); 
                $code =200;

            }
        }
        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$values
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }
    public function api_paypalAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();

        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());

        $user=$request->get("user");
        $id=$request->get("id");
        $key=$request->get("key");
        $plan_id=$request->get("plan");

        $user_obj=$em->getRepository("UserBundle:User")->find($user);
        $pack=$em->getRepository("AppBundle:Pack")->find($plan_id);



        $code = 200;
        $message = "";
        $values= array();
        if ($user_obj != null and $pack != null) {
            if (sha1($user_obj->getPassword()) == $key and $user_obj->isEnabled()) {
                
                $code=201;
                $message="Thank you for your payment, we will notify you when your payment is complete.";
                $values=array();


                $subscription =  new Subscription();
                $subscription->setMethod("paypal");
                $subscription->setPack($pack->getTitle());
                $subscription->setPrice($pack->getPrice());
                $subscription->setCurrency($settings->getCurrency());
                $subscription->setUser($user_obj);
                $subscription->setStatus("pendding");
                $subscription->setTransaction($id);
                $subscription->setDuration($pack->getDuration());
                $em->persist($subscription);
                $em->flush();


                $apiContext = new \PayPal\Rest\ApiContext(
                        new \PayPal\Auth\OAuthTokenCredential(
                            $settings->getPaypalClientid(), // ClientID
                            $settings->getPaypalClientsecret()      // ClientSecret
                        )
                );

                $payment = Payment::get($id, $apiContext);

                $price =  $payment->getTransactions()[0]->getAmount()->getTotal();
                $currency =  $payment->getTransactions()[0]->getAmount()->getCurrency();
                $data =  explode(",",$payment->getTransactions()[0]->getCustom());
                $user_id_pp = str_replace("user:", "",$data[0]);
                $plan_id_pp = str_replace("pack:", "",$data[1]);
                $payer_email =  $payment->getPayer()->getPayerInfo()->getEmail();
                if (
                    $user_id_pp == $user &&
                    $plan_id_pp == $plan_id &&
                    $price == $subscription->getPrice() &&
                    $payment->getState() == "approved" && 
                    strtoupper($currency) == strtoupper($settings->getCurrency())
                ){
                    $started =  new \DateTime();
                    $expired =  new \DateTime();
                    $expired->modify('+'.$subscription->getDuration()." day");

                    $subscription->setStarted($started);
                    $subscription->setExpired($expired);
                    $subscription->setEmail($payer_email);
                    $subscription->setStatus("paid");
                    $em->flush();
                    $code = 200;
                    $message="Congratulations you are now subscribed!";

                }else{
                    $subscription->setStatus("pendding");
                    $em->flush();
                    $code=201;
                    $message="Thank you for your payment, we will notify you when your payment is complete.";
                }
            
            }else {
                $code = 500;
                $message = "The payment not received, Please contact our support team";
            }
        } else {
            $code = 500;
            $message = "The payment not received, Please contact our support team";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$values
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }
    public function api_stripeAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();

        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $user=$request->get("user");
        $id=$request->get("id");
        $key=$request->get("key");
        $plan_id=$request->get("plan");

        $user_obj=$em->getRepository("UserBundle:User")->find($user);
        $pack=$em->getRepository("AppBundle:Pack")->find($plan_id);



        $code = 200;
        $message = "";
        $values= array();
        if ($user_obj != null and $pack != null) {
            if (sha1($user_obj->getPassword()) == $key and $user_obj->isEnabled()) {
                
                $code=201;
                $message="Thank you for your payment, we will notify you when your payment is complete.";
                $values=array();
                    \Stripe\Stripe::setApiKey($settings->getStripeapikey());

                    $intent = \Stripe\PaymentIntent::retrieve($id);
                    $charges = $intent->charges->data;

                    $subscription=$em->getRepository("AppBundle:Subscription")->findOneBy(array("transaction"=>$id));

                    if ($subscription == null) {                
                        $subscription =  new Subscription();
                        $subscription->setMethod("card");
                        $subscription->setPack($pack->getTitle());
                        $subscription->setPrice($pack->getPrice());
                        $subscription->setCurrency($settings->getCurrency());
                        $subscription->setUser($user_obj);
                        $subscription->setStatus("pendding");
                        $subscription->setTransaction($id);
                        $subscription->setDuration($pack->getDuration());
                        $em->persist($subscription);
                        $em->flush(); 
                        if (
                            $charges[0]->metadata->user_id == $user_obj->getId() &&
                            $charges[0]->metadata->plan_id == $pack->getId() &&
                            $charges[0]->amount == $subscription->getPrice()*100 &&
                            $charges[0]->status == "succeeded" && 
                            strtoupper($charges[0]->currency) == strtoupper($settings->getCurrency())
                        ){
                            $started =  new \DateTime();
                            $expired =  new \DateTime();
                            $expired->modify('+'.$subscription->getDuration()." day");

                            $subscription->setStarted($started);
                            $subscription->setExpired($expired);

                            $subscription->setTransaction($intent->id);
                            $subscription->setStatus("paid");
                            $em->flush();
                            $code = 200;
                            $message="Congratulations you are now subscribed!";

                        }else{
                            $subscription->setStatus("pendding");
                            $em->flush();
                            $code=201;
                            $message="Thank you for your payment, we will notify you when your payment is complete.";
                        }  
                        }else{
                             $code = 500;
                            $message = "The payment not received, Please contact our support team";
                        }         
            }else {
                $code = 500;
                $message = "The payment not received, Please contact our support team";
            }
        }else {
            $code = 500;
            $message = "The payment not received, Please contact our support team";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$values
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }
    public function api_cashAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();

        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        
        $user=str_replace('"', "",$request->get("user"));
        $id=str_replace('"', "",$request->get("id"));
        $infos=str_replace('"', "",$request->get("infos"));
        $key=str_replace('"', "",$request->get("key"));
        $plan_id=str_replace('"', "",$request->get("plan"));

        $user_obj=$em->getRepository("UserBundle:User")->find($user);
        $pack=$em->getRepository("AppBundle:Pack")->find($plan_id);

        $code = 200;
        $message = "";
        $values= array();
        if ($user_obj != null and $pack != null) {
            if (sha1($user_obj->getPassword()) == $key and $user_obj->isEnabled()) {
                
                    $code=200;
                    $message="Thank you for your payment, we will notify you when your payment is complete.";
                    $values=array();
                    
                    $subscription=$em->getRepository("AppBundle:Subscription")->findOneBy(array("transaction"=>$id));

                    if ($subscription == null) { 
                        $subscription =  new Subscription();
                        $subscription->setMethod("cash");
                        $subscription->setPack($pack->getTitle());
                        $subscription->setPrice($pack->getPrice());
                        $subscription->setCurrency($settings->getCurrency());
                        $subscription->setUser($user_obj);
                        $subscription->setStatus("pendding");
                        $subscription->setTransaction($id);
                        $subscription->setInfos($infos);
                        $subscription->setDuration($pack->getDuration());
                        if($request->files->get('uploaded_file')){
                            $media= new Media();
                            $media->setFile($request->files->get('uploaded_file'));
                            $media->upload($this->container->getParameter('files_directory'));
                            $media->setEnabled(true);
                            $em->persist($media);
                            $em->flush();
                            $subscription->setMedia($media);
                        }
                        $em->persist($subscription);
                        $em->flush(); 
                    }else{
                         $code = 500;
                        $message = "The payment not received, Please contact our support team";
                    }         
            }else {
                $code = 500;
                $message = "The payment not received, Please contact our support team";
            }
        }else {
            $code = 500;
            $message = "The payment not received, Please contact our support team";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$values
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }
}
?>