<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Item;
use AppBundle\Entity\Support;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

class HomeController extends Controller
{
    public function privacyAction() {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("WebBundle:Home:privacy_policy.html.twig", array("setting" => $setting));
    }
    public function refund_privacyAction() {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("WebBundle:Home:refund_policy.html.twig", array("setting" => $setting));
    }
    public function faqAction() {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("WebBundle:Home:faq.html.twig", array("setting" => $setting));
    }
    public function contactAction(Request $request) {

        $em=$this->getDoctrine()->getManager();
        $support = new Support();
        $form = $this->createFormBuilder($support)
            ->setMethod('POST')
            ->add('email', EmailType::class)
            ->add('subject', TextType::class)
            ->add('message', TextareaType::class)
            ->add('send', SubmitType::class,array("label"=>"Send Message"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($support);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            $support = new Support();
            $form = $this->createFormBuilder($support)
                ->setMethod('POST')
                ->add('email', EmailType::class)
                ->add('subject', TextType::class)
                ->add('message', TextareaType::class)
                ->add('send', SubmitType::class,array("label"=>"Send Message"))
                ->getForm();
        }

        return $this->render("WebBundle:Home:contact.html.twig", array( "form"=>$form->createView()));
    }
    public function ajaxMyListAction(Request $request)
    {
        $code = 500;
        if($request->isXmlHttpRequest()) {
            $em=$this->getDoctrine()->getManager();
            if ($this->getUser()!=null) {
                $id =$request->request->get('id');
                $type =$request->request->get('type');
                if ($type ==  "poster") {
                    $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($poster !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"poster" => $poster));
                        if ($item == null) {
                            
                            $last_item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"channel" =>null),array("position"=>"desc"));
                            $position=1;
                            if ($last_item!=null) {
                                $position=$last_item->getPosition()+1;
                            }
                            $code = 200;
                            $item = new Item();
                            $item->setPoster($poster);
                            $item->setUser($this->getUser());
                            $item->setPosition($position);
                            $em->persist($item);
                            $em->flush();
                        }else{
                            $em->remove($item);
                            $em->flush();
                            $code = 202;
                        }
                    }
                }
                if ($type ==  "channel") {
                    $channel = $em->getRepository("AppBundle:Channel")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($channel !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"channel" => $channel));
                        if ($item == null) {
                            $last_item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$this->getUser(),"poster" =>null),array("position"=>"desc"));
                            $position=1;
                            if ($last_item!=null) {
                                $position=$last_item->getPosition()+1;
                            }


                            $code = 200;
                            $item = new Item();
                            $item->setChannel($channel);
                            $item->setUser($this->getUser());
                            $item->setPosition($position);
                            $em->persist($item);
                            $em->flush();
                        }else{
                            $em->remove($item);
                            $em->flush();
                            $code = 202;
                        }
                    }
                }
            }
        }
        return new Response($code);
    } 
    public function ajaxThemeAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $em=$this->getDoctrine()->getManager();
            $theme =$request->request->get('theme');
            if ($this->getUser()!=null) {
                $user = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$this->getUser()->getId()));
                $user->setTheme($theme);
                $em->flush();
            }

            $session = new Session();
            $session->set('theme', $theme);
        }
        return new Response("ok");

    }

    public function searchAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $q = " ";
        if ($request->query->has("q") and $request->query->get("q") != "") {
            $q .= " AND  p.title like '%" . $request->query->get("q") . "%'";
        }

        $dql = "SELECT p FROM AppBundle:Poster p  WHERE p.enabled = true " . $q . " ORDER BY p.created desc ";
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $posters = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            42
        );

        $repository_channel = $em->getRepository('AppBundle:Channel');
        $repository_actor = $em->getRepository('AppBundle:Actor');


        $query_channel = $repository_channel->createQueryBuilder('c')
            ->where("c.enabled = true","c.title like '%" . $request->query->get("q") . "%'")
            ->addOrderBy('c.created',"desc")
            ->addOrderBy('c.id', 'ASC')
            ->getQuery();
            
        $channels = $query_channel->getResult();

        $query_actor = $repository_actor->createQueryBuilder('c')
            ->where("UPPER(c.slug) like '%" . strtoupper($request->query->get("q")) . "%'")
            ->addOrderBy('c.name',"desc")
            ->addOrderBy('c.id', 'ASC')
            ->getQuery();
        $actors = $query_actor->getResult();


       return $this->render('WebBundle:Home:search.html.twig',array(
            "channels"=>$channels,
            "posters"=>$posters,
            "actors"=>$actors,
            "episodes" =>array()
        ));
    }
    public function mylistAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $channels = $em->getRepository("AppBundle:Item")->findBy(array("poster"=>null,"user"=>$this->getUser()), array("position" => "desc"));
        $poster_size = sizeof($em->getRepository("AppBundle:Item")->findBy(array("channel"=>null), array("position" => "desc")));


        $repository = $em->getRepository('AppBundle:Item');


        $repo_query = $repository->createQueryBuilder('i');

        $repo_query->leftJoin('i.poster', 'p');
        $repo_query->leftJoin('i.channel', 'c');
        $repo_query->where($repo_query->expr()->isNotNull('i.poster'));
        $repo_query->andWhere("p.enabled = true");
        $repo_query->andWhere("i.user =".$this->getUser()->getId());


        $repo_query->addOrderBy('i.position', "desc");
        $repo_query->addOrderBy('p.id', 'ASC');

        $query =  $repo_query->getQuery(); 
        $paginator = $this->get('knp_paginator');
        $posters = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            42
        );
       return $this->render('WebBundle:Home:mylist.html.twig',array(
            "channels"=>$channels,
            "posters"=>$posters,
            "poster_size"=>$poster_size
        ));
    }
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());

        $posters_not_sluged = $em->getRepository("AppBundle:Poster")->notSlug();
        foreach ($posters_not_sluged as $key => $poster) {
                $poster->setTitle($poster->getTitle()." ");
        }
        $em->flush();
        foreach ($posters_not_sluged as $key => $poster) {
                $poster->setTitle(trim($poster->getTitle()));
        }
        $em->flush();

        $channels_not_sluged = $em->getRepository("AppBundle:Channel")->notSlug();
        foreach ($channels_not_sluged as $key => $channel) {
                $channel->setTitle($channel->getTitle()." ");
        }
        $em->flush();

        foreach ($channels_not_sluged as $key => $channel) {
                $channel->setTitle(trim($channel->getTitle()));
        }
        $em->flush();


        $actors_not_sluged = $em->getRepository("AppBundle:Actor")->notSlug();
        foreach ($actors_not_sluged as $key => $actor) {
                $actor->setName($actor->getName()." ");
        }
        $em->flush();
        foreach ($actors_not_sluged as $key => $actor) {
                $actor->setName(trim($actor->getName()));
        }
        $em->flush();

        $episodes_not_sluged = $em->getRepository("AppBundle:Episode")->notSlug();
        foreach ($episodes_not_sluged as $key => $episode) {
                $episode->setTitle($episode->getTitle()." ");
        }
        $em->flush();
        foreach ($episodes_not_sluged as $key => $episode) {
                $episode->setTitle(trim($episode->getTitle()));
        }
        $em->flush();


        $slides = $em->getRepository("AppBundle:Slide")->findBy(array(), array("position" => "asc"));
        $channels = $em->getRepository("AppBundle:Channel")->findBy(array("featured"=>true), array("created" => "desc"),15);
        
        $genres = $em->getRepository("AppBundle:Genre")->findBy(array(), array("position" => "asc"),5);


        $popular = $em->getRepository("AppBundle:Poster")->findBy(array("enabled"=>true), array("views" => "desc"),15);
        $bestrated = $em->getRepository("AppBundle:Poster")->findBy(array("enabled"=>true), array("rating" => "desc"),15);

        $repository = $em->getRepository('AppBundle:Actor');
        $query = $repository->createQueryBuilder('A')
            ->select(array("A.id","A.name","A.type","A.born","A.slug","A.bio","m.url as image","m.extension as extension","SUM(P.views) as test"))
            ->leftJoin('A.roles', 'G')
            ->leftJoin('G.poster', 'P')
            ->leftJoin('A.media', 'm')
            ->groupBy('A.id')
            ->orderBy('test',"DESC")
            ->setMaxResults(20)
            ->getQuery();
        $actors = $query->getResult();
      

        if ($this->getUser()!=null) {
          $user = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$this->getUser()->getId()));
          $session = new Session();
          $session->set('theme', $user->getTheme());
        }

        return $this->render('WebBundle:Home:index.html.twig',array(
            "slides" => $slides,
            "channels"=>$channels,
            "actors"=>$actors,
            "popular"=>$popular,
            "bestrated"=>$bestrated,
            "genres"=>$genres,
            "settings"=>$settings
        ));
    }
    public function headerAction($subtitle,$og_type,$og_image,$keywords,$og_description) {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("WebBundle:Home:header.html.twig", array(
            "subtitle"=>$subtitle,
            "settings" => $settings,
            "og_type" => $og_type,
            "keywords" => $keywords,
            "og_description" => $og_description,
            "og_image"=>$og_image
        )
    );
    }
    public function logoAction() {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("WebBundle:Home:logo.html.twig", array("settings" => $settings));
    }
    public function gplayAction() {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("WebBundle:Home:gplay.html.twig", array("settings" => $settings));
    }
}
