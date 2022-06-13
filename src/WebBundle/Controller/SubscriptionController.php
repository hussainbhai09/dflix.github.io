<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\Support;
use MediaBundle\Entity\Media;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use WebBundle\Form\ManualType;


class SubscriptionController extends Controller
{
    public function notifyAction(Request $request){
            
            $em=$this->getDoctrine()->getManager();


            $link ="https://www.sandbox.paypal.com/cgi-bin/webscr"  ;  
            $raw_post_data = file_get_contents('php://input');
            $raw_post_array = explode('&', $raw_post_data);
            $myPost = array();
            foreach ($raw_post_array as $keyval) {
                $keyval = explode ('=', $keyval);
                if (count($keyval) == 2)
                    $myPost[$keyval[0]] = urldecode($keyval[1]);
            }

            $req = 'cmd=_notify-validate';
            if(function_exists('get_magic_quotes_gpc')) {
                $get_magic_quotes_exists = true;
            }
            foreach ($myPost as $key => $value) {
                if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                    $value = urlencode(stripslashes($value));
                } else {
                    $value = urlencode($value);
                }
                $req .= "&$key=$value";
            }

            /*
             * Post IPN data back to PayPal to validate the IPN data is genuine
             * Without this step anyone can fake IPN data
             */
            $paypalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
            $ch = curl_init($paypalURL);
            if ($ch == FALSE) {
                return FALSE;
            }
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

            // Set TCP timeout to 30 seconds
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: company-name'));
            $res = curl_exec($ch);
                
            $tokens = explode("\r\n\r\n", trim($res));
            $res = trim(end($tokens));

            if (strcmp($res, "VERIFIED") == 0 || strcasecmp($res, "VERIFIED") == 0) {
                $txn_id = !empty($request->get('txn_id'))?$request->get('txn_id'):'';
                
                if(!empty($txn_id)){
                    $payment_status = !empty($request->get('payment_status'))?$request->get('payment_status'):'';
                    $currency_code = $request->get('mc_currency');
                    $payment_gross =  !empty($request->get('mc_gross'))?$request->get('mc_gross'):0;
                    $item_number = $request->get('item_number');

                    $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("id"=>$item_number,"method"=>"paypal","status"=>"unpaid"));

                    if (
                        $payment_status == "Completed" and 
                        $currency_code == $subscription->getCurrency() and
                        $payment_gross == $subscription->getPrice()
                    ) {
                        $subscr_id = $request->get('subscr_id');
                        $payer_email = $request->get('payer_email');
                        $payer_id = $request->get('payer_id');
                        $item_name = $request->get('item_name');
                        
                        $subscription->setEmail($payer_email);
                        $subscription->setStatus("paid");
                        $subscription->setTransaction($txn_id);

                        $started =  new \DateTime();
                        $expired =  new \DateTime();
                        $expired->modify('+'.$subscription->getDuration()." day");

                        $subscription->setStarted($started);
                        $subscription->setExpired($expired);

                        $em->flush();
                    }
                }

            }   
            return new Response("done"); 

    }
    public function finishAction(Request $request,$id){
        $em=$this->getDoctrine()->getManager();
        $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("user"=>$this->getUser(),"id"=>$id));
        if ($subscription == null) {
            throw new NotFoundHttpException("Page not found");  
        }
        return $this->render('WebBundle:Subscription:finish.html.twig',array("subscription"=>$subscription));
    }
    public function paypal_finishAction(Request $request,$id){
        $em=$this->getDoctrine()->getManager();
        $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("user"=>$this->getUser(),"id"=>$id));
        if ($subscription == null) {
            throw new NotFoundHttpException("Page not found");  
        }
        return $this->render('WebBundle:Subscription:paypal_finish.html.twig',array("subscription"=>$subscription));
    }
    public function charge_ajaxAction(Request $request){
        if($request->isXmlHttpRequest()) {
            $id =$request->request->get('id');
            $intent_id =$request->request->get('intent');
            $em=$this->getDoctrine()->getManager();
            $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("user"=>$this->getUser(),"id"=>$id,"method"=>"card","status"=>"unpaid"));
            $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());

            \Stripe\Stripe::setApiKey($settings->getStripeapikey());

            $intent = \Stripe\PaymentIntent::retrieve($intent_id);
            $charges = $intent->charges->data;
            $code = 400;
            if (
                $charges[0]->metadata->subscription_id == $subscription->getId() &&
                $charges[0]->amount == $subscription->getPrice()*100 &&
                $charges[0]->status == "succeeded" && 
                strtoupper($charges[0]->currency) == strtoupper($settings->getCurrency())
            ){
                $started =  new \DateTime();
                $expired =  new \DateTime();
                $expired->modify('+'.$subscription->getDuration()." day");

                $subscription->setStarted($started);
                $subscription->setExpired($expired);

                $subscription->setTransaction($charges[0]->id);
                $subscription->setStatus("paid");
                $em->flush();
                $code = 200;
            }
            return new Response($code);
        }
    }
    public function cardAction(Request $request,$id){

        $em=$this->getDoctrine()->getManager();
        $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("user"=>$this->getUser(),"id"=>$id,"method"=>"card","status"=>"unpaid"));
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());
        if ($subscription == null) {
            throw new NotFoundHttpException("Page not found");  
        }
        \Stripe\Stripe::setApiKey($settings->getStripeapikey());

        $intent = \Stripe\PaymentIntent::create(
            array(
                "amount" => $subscription->getPrice() * 100,
                "currency" => strtolower($settings->getCurrency()),
                "metadata" => array("subscription_id" => $subscription->getId()),
                "setup_future_usage" => "off_session"
            )
        );

        if ($subscription == null) {
            throw new NotFoundHttpException("Page not found");  
        }
        return $this->render('WebBundle:Subscription:card.html.twig',array("subscription"=>$subscription,"settings"=>$settings,"intent"=>$intent));
    }
    public function cancelAction(Request $request,$id){
        $em=$this->getDoctrine()->getManager();
        $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("user"=>$this->getUser(),"id"=>$id));
        if ($subscription == null) {
            throw new NotFoundHttpException("Page not found");  
        }
        return $this->render('WebBundle:Subscription:cancel.html.twig',array("subscription"=>$subscription));
    }
    public function manualAction(Request $request,$id){

        $em=$this->getDoctrine()->getManager();

        $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("user"=>$this->getUser(),"id"=>$id,"method"=>"manual","status"=>"unpaid"));
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        if ($subscription == null) {
            throw new NotFoundHttpException("Page not found");  
        }
        $form = $this->createForm(ManualType::class,$subscription);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if ($subscription->getFile()!=null) {               
                    $media= new Media();
                    $media->setFile($subscription->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                    $subscription->setMedia($media);
                }
                $subscription->setStatus("pendding");

                $em->persist($subscription);
                $em->flush();
                return $this->redirect($this->generateUrl('wep_subscription_finish',array("id"=>$subscription->getId())));

        }
        return $this->render(
            'WebBundle:Subscription:manual.html.twig',
            array(
                "form"=>$form->createView(),
                "subscription"=>$subscription,
                 "account"=>$settings->getCashaccount()

            )
        );
    }

	 public function subscribeAction(Request $request){

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();

        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());
        $methods =  array();
        if ($settings->getPaypal()) {
            $methods[" PayPal"] ="paypal" ;
        }
        if ($settings->getStripe()) {
            $methods[" Credit Card"] = "card";
        }
        if ($settings->getManual()) {
            $methods["Manual / Cash Payment"] = "manual";
        }

        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('packs', EntityType::class, 
                array( 'expanded' => true,
                'by_reference' => false,
                'class' => 'AppBundle:Pack')
            )           
            ->add('method' ,ChoiceType::class, array(
                'expanded' => true,
                'by_reference' => false,
                'choices' => $methods))
            ->getForm();
        $form->handleRequest($request);

        $packs = $em->getRepository("AppBundle:Pack")->findAll();

    
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pack =  $data["packs"];

            if ($data["packs"] =! null && $data["method"] == "paypal") {
                $subscription =  new Subscription();
                $subscription->setMethod("paypal");
                $subscription->setPack($pack->getTitle());
                $subscription->setPrice($pack->getPrice());
                $subscription->setCurrency($settings->getCurrency());
                $subscription->setUser($this->getUser());
                $subscription->setStatus("unpaid");
                $subscription->setDuration($pack->getDuration());
                $em->persist($subscription);
                $em->flush();
                return $this->redirect($this->generateUrl('wep_subscription_paypal',array("id"=>$subscription->getId())));
            }
            if ($data["packs"] =! null && $data["method"] == "card") {
                $subscription =  new Subscription();
                $subscription->setMethod("card");
                $subscription->setPack($pack->getTitle());
                $subscription->setPrice($pack->getPrice());
                $subscription->setCurrency($settings->getCurrency());
                $subscription->setUser($this->getUser());
                $subscription->setStatus("unpaid");
                $subscription->setDuration($pack->getDuration());
                $em->persist($subscription);
                $em->flush();
                return $this->redirect($this->generateUrl('wep_subscription_card',array("id"=>$subscription->getId())));
            }
            if ($data["packs"] =! null && $data["method"] == "manual") {

                $subscription =  new Subscription();
                $subscription->setMethod("manual");
                $subscription->setPack($pack->getTitle());
                $subscription->setPrice($pack->getPrice());
                $subscription->setCurrency($settings->getCurrency());
                $subscription->setUser($this->getUser());
                $subscription->setStatus("unpaid");
                $subscription->setDuration($pack->getDuration());
                $em->persist($subscription);
                $em->flush();
                return $this->redirect($this->generateUrl('wep_subscription_manual',array("id"=>$subscription->getId())));
            }
        }
        return $this->render('WebBundle:Subscription:subscribe.html.twig',array( 
                "form"=>$form->createView(),
                "packs" => $packs,
                "currency"=>$settings->getCurrency()
            )
        );
    }
    public function paypalAction(Request $request,$id){
        $em=$this->getDoctrine()->getManager();
        $subscription = $em->getRepository("AppBundle:Subscription")->findOneBy(array("user"=>$this->getUser(),"id"=>$id,"method"=>"paypal","status"=>"unpaid"));
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        if ($subscription == null) {
            throw new NotFoundHttpException("Page not found");  
        }
        
        $link = ($settings->getPaypalsandbox())? "https://www.sandbox.paypal.com/cgi-bin/webscr": "https://www.paypal.com/cgi-bin/webscr";
        $currency  = $settings->getCurrency();
        $price  =$subscription->getPrice();
        $pack  = $subscription->getPack();
        $account = $settings->getPaypalaccount();
        return $this->render('WebBundle:Subscription:paypal.html.twig',array(
                "id" => $id,
                "link" => $link,
                "currency" => $currency,
                "pack" => $pack,
                "price" => $price,
                "account" => $account,
                "subscription"=>$subscription
        ));
    }
}
