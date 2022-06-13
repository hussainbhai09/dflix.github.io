<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Rate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class RateController extends Controller
{
    public function ajaxAddAction(Request $request)
    {
		if($request->isXmlHttpRequest()) {
	            $id =$request->request->get('id');
            	$review = htmlspecialchars($request->request->get('review'));
	            $rating =$request->request->get('rating');
	            $type =$request->request->get('type');
	            $em=$this->getDoctrine()->getManager();
	            if ($type ==  "poster") {
	            	 $object = $em->getRepository('AppBundle:Poster')->find($id);
	            }else{
	            	 $object = $em->getRepository('AppBundle:Channel')->find($id);
	            }
	            if ($object!=null) {
	            	if ($type ==  "poster") {
	                	$rate = $em->getRepository('AppBundle:Rate')->findOneBy(array("user" => $this->getUser(), "poster" => $object));
	                }else{
	                	$rate = $em->getRepository('AppBundle:Rate')->findOneBy(array("user" => $this->getUser(), "channel" => $object));
	                }
	                if ($rate == null) {
	                    $rate_obj = new Rate();
	                    $rate_obj->setValue($rating);
	                    if ($type ==  "poster") {
	                    	$rate_obj->setPoster($object);
	                    }else{
	                    	$rate_obj->setChannel($object);
						}
	                    $rate_obj->setUser($this->getUser());
	                    $rate_obj->setReview($review);
	                    $em->persist($rate_obj);
	                    $em->flush();
	                    $message = "Your Ratting has been added";
	                } else {
	                    $rate->setValue($rating);
	                    $rate->setReview($review);
	                    $em->flush();
	                    $message = "Your Ratting has been edit"; 
	                }
	            	if ($type ==  "poster") {
	                	$rates = $em->getRepository('AppBundle:Rate')->findBy(array("poster" => $object));
	                }else{
	                	$rates = $em->getRepository('AppBundle:Rate')->findBy(array("channel" => $object));
	                }
	                $total = 0;
	                $count = 0;
	                foreach ($rates as $key => $r) {
	                    $total += $r->getValue();
	                    $count++;
	                }
	                $v = 0;
	                if ($count != 0) {
	                    $v = $total / $count;
	                }
	                $v2 = number_format((float) $v, 1, '.', '');
	                $errors[] = array("name" => "rate", "value" => $v2);
	                $object->setRating($v2);
	                $em->flush();

	               	$code = "200";
	               	$value = $v2;
	        } else {
        		$value =0;
	            $code = 0;
	            $message = "Sorry, your rate could not be added at this time";
	        }

        }else{
        		$value =0;
        		$code = "500";
	            $message = "Sorry, your rate could not be added at this time";
        }
	    $response = array(
            "code" => $code,
            "message" => $message,
            "value" => $value,
        );
	        $encoders = array(new XmlEncoder(), new JsonEncoder());
	        $normalizers = array(new ObjectNormalizer());
	        $serializer = new Serializer($normalizers, $encoders);
	        $jsonContent = $serializer->serialize($response, 'json');
	        return new Response($jsonContent);
    }
}
