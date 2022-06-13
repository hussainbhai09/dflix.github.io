<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
class CommentController extends Controller
{
    public function ajaxAddAction(Request $request)
    {

		if($request->isXmlHttpRequest()) {
            $id =$request->request->get('id');
                $type =$request->request->get('type');

            $text = htmlspecialchars($request->request->get('comment'));
            $em=$this->getDoctrine()->getManager();
            if ($type ==  "poster") {
                 $object = $em->getRepository('AppBundle:Poster')->find($id);
            }else{
                 $object = $em->getRepository('AppBundle:Channel')->find($id);
            }
            $imagineCacheManager = $this->get('liip_imagine.cache.manager');

            if ($object!=null) {
				$comment = new Comment();
                $comment->setContent(base64_encode($text));
                $comment->setEnabled(true);
                $comment->setUser($this->getUser());
                if ($type ==  "poster") {
                    $comment->setPoster($object);
                }else{
                    $comment->setChannel($object);
                }
                $em->persist($comment);
                $em->flush();  
					$jsonObj["id"]=$comment->getId();
                $jsonObj["content"]=$comment->getContentclear();
                $jsonObj["user"]=$comment->getUser()->getName();
                if($comment->getUser()->getMedia() ==  null ){
                    $jsonObj["image"]="https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg";   
                }else{
                    if ($comment->getUser()->getMedia()->getType()=="link") {
                        $jsonObj["image"]=$comment->getUser()->getMedia()->getUrl();   
                    }else{
                        $jsonObj["image"]=$imagineCacheManager->getBrowserPath($comment->getUser()->getMedia()->getLink(), 'actor_thumb') ;   
                    }
                }
                $jsonObj["created"]="now";
		        $encoders = array(new XmlEncoder(), new JsonEncoder());
		        $normalizers = array(new ObjectNormalizer());
		        $serializer = new Serializer($normalizers, $encoders);
		        $jsonContent=$serializer->serialize($jsonObj, 'json');
		        return new Response($jsonContent);
            }else{
            	return new Response("400");
            }

        }
    }
}
