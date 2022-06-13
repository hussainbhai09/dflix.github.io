<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ChannelController extends Controller
{

public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $order =  $request->get('order');
        $category =  $request->get('category');
        $country =  $request->get('country');

        $category = $em->getRepository('AppBundle:Category')->findOneBy(array("title"=>$request->get('category')));
        $country = $em->getRepository('AppBundle:Country')->findOneBy(array("title"=>$request->get('country')));

        $repository = $em->getRepository('AppBundle:Channel');


        $dir = "DESC";
        if($order == "title"){
            $dir="ASC";
        }elseif ($order == "newest") {
            $order = "created";
        }

        if($category==null && $country==null){
            $query = $repository->createQueryBuilder('c')
                ->where("c.enabled = true")
                ->addOrderBy('c.'.$order, $dir)
                ->addOrderBy('c.id', 'ASC')
                ->getQuery();         
        }else if ($category!=null && $country==null){
            $query = $repository->createQueryBuilder('c')
                ->leftJoin('c.categories', 'g')
                ->where("c.enabled = true",'g.id = ' . $category->getId())
                ->addOrderBy('c.'.$order, $dir)
                ->addOrderBy('c.id', 'ASC')
                ->getQuery();         
        }else if ($category==null && $country!=null){
            $query = $repository->createQueryBuilder('c')
                ->leftJoin('c.countries', 'g')
                ->where("c.enabled = true",'g.id = ' . $country->getId())
                ->addOrderBy('c.'.$order, $dir)
                ->addOrderBy('c.id', 'ASC')
                ->getQuery();         
        }else if ($category!=null && $country!=null){
            $query = $repository->createQueryBuilder('c')
                ->leftJoin('c.countries', 'cn')
                ->leftJoin('c.categories', 'cg')
                ->where("c.enabled = true",'cn.id = ' . $country->getId(),'cg.id = ' . $category->getId())
                ->addOrderBy('c.'.$order, $dir)
                ->addOrderBy('c.id', 'ASC')
                ->getQuery();         
        }

        $paginator = $this->get('knp_paginator');
        $channels = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            42
        );


        $categories = $em->getRepository('AppBundle:Category')->findAll();
        $countries = $em->getRepository('AppBundle:Country')->findAll();

        return $this->render('WebBundle:Channel:index.html.twig',
            array(
                "channels"=>$channels,
                "categories"=>$categories,
                "countries"=>$countries
            )
        );
    }
    public function shareAction($id)
    {


       
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy(array());
        $channel = $em->getRepository('AppBundle:Channel')->findOneBy(array("id"=>$id,"enabled"=>true));
        $comments = $em->getRepository('AppBundle:Comment')->findBy(array("channel"=>$channel),array("created"=>"asc"));
        if ($channel == null) {
             throw new NotFoundHttpException("Page not found");  
        }


        $repository_soure = $em->getRepository('AppBundle:Source');
        $repo_query_source = $repository_soure->createQueryBuilder('c');
        $repo_query_source->leftJoin('c.channel', 'ch');
        $repo_query_source->where("ch.id = ". $id ,"c.kind like 'play' or c.kind like 'both' " );
   

        $favorited =  false;
        
        $premium =  false;
        if ($this->getUser()!=null) {
           $premium = $this->getUser()->isSubscribed();
           $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"channel" => $channel));
           if ($item!= null) {
               $favorited =  true;
           }

           if (!$premium) {
                $repo_query_source->andWhere("c.premium like '1' or c.premium like '3'");
           }
        }else{
               $repo_query_source->andWhere("c.premium like '1' or c.premium like '3'");
        }

        $query_source =  $repo_query_source->getQuery(); 
        $sources = $query_source->getResult();
        $categories="";
        foreach ($channel->getCategories() as $key => $category) {
            $categories .= ",".$category->getId();
        }
        $categories = trim($categories,",");

        $countries="";
        foreach ($channel->getCountries() as $key => $country) {
            $countries .= ",".$country->getId();
        }
        $countries = trim($countries,",");
        $countries=($countries=="")? "0":$countries;
        $categories=($categories=="")? "0":$categories;

        $nombre = 20;
        $repository = $em->getRepository('AppBundle:Channel');
        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->where("p.enabled = true",'c.id in ('.$categories.')')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults($nombre)
            ->getQuery();
        $related_channels = $query->getResult();

        return $this->render('WebBundle:Channel:view.html.twig',array(
            "channel" => $channel,
            "comments" => $comments,
            "related_channels"=>$related_channels,
            "favorited"=>$favorited,
            "sources"=>$sources,
            "settings"=>$settings
        ));
    }
    public function viewAction($id,$slug)
    {


       
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy(array());
        $channel = $em->getRepository('AppBundle:Channel')->findOneBy(array("id"=>$id,"slug"=>$slug,"enabled"=>true));
      	$comments = $em->getRepository('AppBundle:Comment')->findBy(array("channel"=>$channel),array("created"=>"asc"));
        if ($channel == null) {
        	 throw new NotFoundHttpException("Page not found");  
        }


        $repository_soure = $em->getRepository('AppBundle:Source');
        $repo_query_source = $repository_soure->createQueryBuilder('c');
        $repo_query_source->leftJoin('c.channel', 'ch');
        $repo_query_source->where("ch.id = ". $id ,"c.kind like 'play' or c.kind like 'both' " );
   

        $favorited =  false;
        
        $premium =  false;
        if ($this->getUser()!=null) {
           $premium = $this->getUser()->isSubscribed();
           $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"channel" => $channel));
           if ($item!= null) {
               $favorited =  true;
           }

           if (!$premium) {
                $repo_query_source->andWhere("c.premium like '1' or c.premium like '3'");
           }
        }else{
               $repo_query_source->andWhere("c.premium like '1' or c.premium like '3'");
        }

        $query_source =  $repo_query_source->getQuery(); 
        $sources = $query_source->getResult();
        $categories="";
        foreach ($channel->getCategories() as $key => $category) {
        	$categories .= ",".$category->getId();
        }
        $categories = trim($categories,",");

        $countries="";
        foreach ($channel->getCountries() as $key => $country) {
            $countries .= ",".$country->getId();
        }
        $countries = trim($countries,",");
        $countries=($countries=="")? "0":$countries;
        $categories=($categories=="")? "0":$categories;

        $nombre = 20;
        $repository = $em->getRepository('AppBundle:Channel');
        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->where("p.enabled = true",'c.id in ('.$categories.')')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults($nombre)
            ->getQuery();
        $related_channels = $query->getResult();

        /* get all play sources */


        $repository_all_source = $em->getRepository('AppBundle:Source');
        $repo_query_all_source = $repository_all_source->createQueryBuilder('c');
        $repo_query_all_source->leftJoin('c.channel', 'p');
        $repo_query_all_source->where("p.id = ". $id ,"c.kind like 'play' or c.kind like 'both' " );
        $query_all_source =  $repo_query_all_source->getQuery(); 
        $all_sources = $query_all_source->getResult();

        /* end get all play sources */

        return $this->render('WebBundle:Channel:view.html.twig',array(
            "channel" => $channel,
            "comments" => $comments,
            "related_channels"=>$related_channels,
            "favorited"=>$favorited,
            "sources"=>$sources,
            "all_sources"=>$all_sources,
            "settings"=>$settings
        ));
    }


}
