<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MovieController extends Controller
{

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $order =  $request->get('order');
        $genre =  $request->get('genre');

        $genre = $em->getRepository('AppBundle:Genre')->findOneBy(array("title"=>$request->get('genre')));

        $nombre = 30;
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $repository = $em->getRepository('AppBundle:Poster');


        $dir = "DESC";
        if($order == "title"){
            $dir="ASC";
        }elseif ($order == "newest") {
            $order = "created";
        }
        $repo_query = $repository->createQueryBuilder('p');
        $repo_query->where("p.enabled = true");

        if ($genre != null) {
                $repo_query->leftJoin('p.genres', 'g');
                $repo_query->andWhere('g.id = ' . $genre->getId());
        }

        $repo_query->andWhere("p.type like 'movie'");
        

        $repo_query->addOrderBy('p.'.$order, $dir);
        $repo_query->addOrderBy('p.id', 'ASC');

        $query =  $repo_query->getQuery(); 
        $paginator = $this->get('knp_paginator');
        $posters = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            42
        );


        $genres = $em->getRepository('AppBundle:Genre')->findAll();

        return $this->render('WebBundle:Movie:index.html.twig',
            array(
                "posters"=>$posters,
                "genres"=>$genres
            )
        );
    }
    public function filterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $order =  $request->get('order');
        $genre =  $request->get('genre');

        $genre = $em->getRepository('AppBundle:Genre')->findOneBy(array("title"=>$request->get('genre')));

        $nombre = 30;
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $repository = $em->getRepository('AppBundle:Poster');


        $dir = "DESC";
        if($order == "title"){
            $dir="ASC";
        }elseif ($order == "newest") {
            $order = "created";
        }
        $repo_query = $repository->createQueryBuilder('p');
        $repo_query->where("p.enabled = true");

        if ($genre != null) {
                $repo_query->leftJoin('p.genres', 'g');
                $repo_query->andWhere('g.id = ' . $genre->getId());
        }



        $repo_query->addOrderBy('p.'.$order, $dir);
        $repo_query->addOrderBy('p.id', 'ASC');

        $query =  $repo_query->getQuery(); 
        $paginator = $this->get('knp_paginator');
        $posters = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            42
        );


        $genres = $em->getRepository('AppBundle:Genre')->findAll();

        return $this->render('WebBundle:Movie:filter.html.twig',
            array(
                "posters"=>$posters,
                "genres"=>$genres
            )
        );
    }
    public function shareAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy(array());
      
        $poster = $em->getRepository('AppBundle:Poster')->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($poster == null) {
            throw new NotFoundHttpException("Page not found");  
        }

        $repository_soure = $em->getRepository('AppBundle:Source');
        $repo_query_source = $repository_soure->createQueryBuilder('c');
        $repo_query_source->leftJoin('c.poster', 'p');
        $repo_query_source->where("p.id = ". $id ,"c.kind like 'play' or c.kind like 'both' " );
   

        $favorited =  false;
        $premium =  false;
        if ($this->getUser()!=null) {
           $premium = $this->getUser()->isSubscribed();
           $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"poster" => $poster));
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

        $comments = $em->getRepository('AppBundle:Comment')->findBy(array("poster"=>$poster),array("created"=>"asc"));
        $ratings = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$poster),array("created"=>"asc"));

        $genres="";
        foreach ($poster->getGenres() as $key => $genre) {
            $genres .= ",".$genre->getId();
        }
        $genres = trim($genres,",");
        $genres=($genres=="")? "0":$genres;
        $nombre = 20;
        $repository = $em->getRepository('AppBundle:Poster');
        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.genres', 'g')
            ->where("p.enabled = true","p.type like 'movie' ",'g.id in ('.$genres.')')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults($nombre)
            ->getQuery();
        $related_posters = $query->getResult();
        $layout  = ($poster->gettype() == "movie")?'WebBundle:Movie:view.html.twig':'WebBundle:Serie:view.html.twig';
        return $this->render($layout,array(
            "poster" => $poster,
            "comments"=>$comments,
            "related_posters"=>$related_posters,
            "favorited"=>$favorited,
            "sources"=>$sources,
            "settings"=>$settings,
            "ratings"=>$ratings,

        ));
    }
    public function viewAction($id,$slug)
    {

        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy(array());
      
        $poster = $em->getRepository('AppBundle:Poster')->findOneBy(array("type"=>"movie","id"=>$id,"slug"=>$slug,"enabled"=>true));
        if ($poster == null) {
            throw new NotFoundHttpException("Page not found");  
        }

        $repository_soure = $em->getRepository('AppBundle:Source');
        $repo_query_source = $repository_soure->createQueryBuilder('c');
        $repo_query_source->leftJoin('c.poster', 'p');
        $repo_query_source->where("p.id = ". $id ,"c.kind like 'play' or c.kind like 'both' " );
   

        $favorited =  false;
        $premium =  false;
        if ($this->getUser()!=null) {
           $premium = $this->getUser()->isSubscribed();
           $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"poster" => $poster));
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

        $comments = $em->getRepository('AppBundle:Comment')->findBy(array("poster"=>$poster),array("created"=>"asc"));
        $ratings = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$poster),array("created"=>"asc"));
        /* get all play sources */


        $repository_all_source = $em->getRepository('AppBundle:Source');
        $repo_query_all_source = $repository_all_source->createQueryBuilder('c');
        $repo_query_all_source->leftJoin('c.poster', 'p');
        $repo_query_all_source->where("p.id = ". $id ,"c.kind like 'play' or c.kind like 'both' " );
        $query_all_source =  $repo_query_all_source->getQuery(); 
        $all_sources = $query_all_source->getResult();

        /* end get all play sources */
        /* get all play sources */


        $repository_all_source_download = $em->getRepository('AppBundle:Source');
        $repo_query_all_source_download = $repository_all_source_download->createQueryBuilder('c');
        $repo_query_all_source_download->leftJoin('c.poster', 'p');
        $repo_query_all_source_download->where("p.id = ". $id ,"c.kind like 'download' or c.kind like 'both' " );
        $query_all_source_download =  $repo_query_all_source_download->getQuery(); 
        $all_source_downloads = $query_all_source_download->getResult();

        /* end get all play sources */



        $genres="";
        foreach ($poster->getGenres() as $key => $genre) {
        	$genres .= ",".$genre->getId();
        }
        $genres = trim($genres,",");
        $genres=($genres=="")? "0":$genres;
        $nombre = 20;
        $repository = $em->getRepository('AppBundle:Poster');
        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.genres', 'g')
            ->where("p.enabled = true","p.type like 'movie' ",'g.id in ('.$genres.')')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults($nombre)
            ->getQuery();
        $related_posters = $query->getResult();

        return $this->render('WebBundle:Movie:view.html.twig',array(
            "poster" => $poster,
            "comments"=>$comments,
            "ratings"=>$ratings,
            "related_posters"=>$related_posters,
            "favorited"=>$favorited,
            "sources"=>$sources,
            "settings"=>$settings,
            "all_sources"=> $all_sources,
            "all_source_downloads" =>$all_source_downloads
        ));
    }

    public function subtitlesAction($id)
    {       
        $em = $this->getDoctrine()->getManager();

        $poster = $em->getRepository('AppBundle:Poster')->findOneBy(array("id"=>$id));

        return $this->render('WebBundle:Movie:subtitles.html.twig',array(
            "poster"=>$poster
        ));
    }
    public function downloadsAction($id,Request $request)
    {     

        if(!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException("Page not found");  
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Source');
        $repo_query = $repository->createQueryBuilder('c');
        $repo_query->leftJoin('c.poster', 'p');
        $repo_query->where("p.id = ". $id ,"c.kind like 'download' or c.kind like 'both' " );

        $query =  $repo_query->getQuery(); 
        $sources = $query->getResult();
        $premium =  false;
        if ($this->getUser()!=null) {
           $premium = $this->getUser()->isSubscribed();
       }
        return $this->render('WebBundle:Movie:downloads.html.twig',array(
            "sources"=>$sources,
            "premium"=>$premium,
        ));
    }
}
