<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ActorController extends Controller
{

	public function indexAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
        $order =  $request->get('order');


        $nombre = 30;
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        
        $repository = $em->getRepository('AppBundle:Actor');


        $dir = "DESC";
        if($order == "title"){
            $dir="ASC";
        }elseif ($order == "newest") {
            $order = "created";
        }
        $repo_query = $repository->createQueryBuilder('p');


        


        $query =  $repo_query->getQuery(); 
        $paginator = $this->get('knp_paginator');
        $actors = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            42
        );



        return $this->render('WebBundle:Actor:index.html.twig',
            array(
                "actors"=>$actors
            )
        );

    }
  
    public function viewAction($id,$slug)
    {
        $em = $this->getDoctrine()->getManager();
      	$actor = $em->getRepository('AppBundle:Actor')->findOneBy(array("id"=>$id,"slug"=>$slug));

        $nombre = 30;
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $repository = $em->getRepository('AppBundle:Poster');
        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.roles', 'r')
            ->leftJoin('r.actor', 'u')
            ->where("p.enabled = true","u.id  = ".$id)
            ->addOrderBy('p.created', 'DESC')
            ->addOrderBy('p.id', 'ASC')
            ->setMaxResults($nombre)
            ->getQuery();
        $related_posters = $query->getResult();

        return $this->render('WebBundle:Actor:view.html.twig',array(
            "actor" => $actor,
            "related_posters"=>$related_posters
            
        ));
    }

}
