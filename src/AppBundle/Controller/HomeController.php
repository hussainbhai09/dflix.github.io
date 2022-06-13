<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Device;
use AppBundle\Entity\Item;

use MediaBundle\Entity\Media;
use AppBundle\Form\WebSettingsType;
use AppBundle\Form\SettingsType;
use AppBundle\Form\AdsType;
use AppBundle\Form\WebAdsType;
use AppBundle\Form\PaymentType;
use AppBundle\Form\FaqType;
use AppBundle\Form\RefundType;
use AppBundle\Form\PolicyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class HomeController extends Controller
{
    function send_notificationToken ($tokens, $message,$key)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids'  => $tokens,
            'data'   => $message

            );
        $headers = array(
            'Authorization:key = '.$key,
            'Content-Type: application/json'
            );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
    }
    function send_notification ($tokens, $message,$key)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to'  => '/topics/Flixo',
            'data'   => $message
            );
        $headers = array(
            'Authorization:key = '.$key,
            'Content-Type: application/json'
            );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
    }
      public function privacypolicyAction() {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("AppBundle:Home:privacypolicy.html.twig", array("setting" => $setting));
    }
    public function notifChannelAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->add('object', EntityType::class, array('class' => 'AppBundle:Channel'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selected_channel = $em->getRepository("AppBundle:Channel")->find($data["object"]);
            $message = array(
                  "type"=> "channel",
                  "id"=> $selected_channel->getId(),
                  "title"=> $data["title"],
                  "message"=>$data["message"],
                  "image"=> $data["image"],
                  "icon"=>$data["icon"]
                );

            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_image = $this->send_notification(null, $message,$key); 
            $this->addFlash('success', 'Operation has been done successfully ');
        }
        return $this->render('AppBundle:Home:notif_channel.html.twig',array(
          "form"=>$form->createView()
          ));
    }

    public function notifPosterAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $em=$this->getDoctrine()->getManager();
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->add('object', EntityType::class, array('class' => 'AppBundle:Poster'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selected_poster = $em->getRepository("AppBundle:Poster")->find($data["object"]);
            $message = array(
                  "type"=> "poster",
                  "id"=> $selected_poster->getId(),
                  "title"=> $data["title"],
                  "message"=>$data["message"],
                  "image"=> $data["image"],
                  "icon"=>$data["icon"]
                );

            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_image = $this->send_notification(null, $message,$key); 
            $this->addFlash('success', 'Operation has been done successfully ');
        }
        return $this->render('AppBundle:Home:notif_poster.html.twig',array(
          "form"=>$form->createView()
          ));
    }
    public function notifGenreAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');



        $em=$this->getDoctrine()->getManager();
        $genres= $em->getRepository("AppBundle:Genre")->findAll();


        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->add('genre', EntityType::class, array('class' => 'AppBundle:Genre'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            $genre_selected = $em->getRepository("AppBundle:Genre")->find($data["genre"]);

            $message = array(
                        "type"=>"genre",
                        "id"=>$genre_selected->getId(),
                        "title_genre"=>$genre_selected->getTitle(),
                        "title"=> $data["title"],
                        "message"=>$data["message"],
                        "image"=> $data["image"],
                        "icon"=>$data["icon"]
                        );
            
            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();

            $message_video = $this->send_notification(null, $message,$key); 
            
            $this->addFlash('success', 'Operation has been done successfully ');

        }
        return $this->render('AppBundle:Home:notif_genre.html.twig',array(
          "form"=>$form->createView()
          ));
    }
    public function notifCategoryAction(Request $request)
    {
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');



        $em=$this->getDoctrine()->getManager();
        $categories= $em->getRepository("AppBundle:Category")->findAll();



        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
           # ->add('url', UrlType::class)
           # ->add('categories', ChoiceType::class, array('choices' => $categories ))           
            ->add('category', EntityType::class, array('class' => 'AppBundle:Category'))           
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            $category_selected = $em->getRepository("AppBundle:Category")->find($data["category"]);

            $message = array(
                        "type"=>"category",
                        "id"=>$category_selected->getId(),
                        "title_category"=>$category_selected->getTitle(),
                        "title"=> $data["title"],
                        "message"=>$data["message"],
                        "image"=> $data["image"],
                        "icon"=>$data["icon"]
                        );
            
            $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();

            $message_video = $this->send_notification(null, $message,$key); 
            
            $this->addFlash('success', 'Operation has been done successfully ');

        }
        return $this->render('AppBundle:Home:notif_category.html.twig',array(
          "form"=>$form->createView()
          ));
    }
   public function notifUrlAction(Request $request)
    {
    
        memory_get_peak_usage();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        $em=$this->getDoctrine()->getManager();

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('GET')
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)      
            ->add('url', UrlType::class,array("label"=>"Url"))
            ->add('icon', UrlType::class,array("label"=>"Large Icon","required"=>false))
            ->add('image', UrlType::class,array("label"=>"Big Picture","required"=>false))
            ->add('send', SubmitType::class,array("label"=>"Send notification"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = array(
                        "type"=>"link",
                        "id"=>strlen($data["url"]),
                        "link"=>$data["url"],
                        "title"=> $data["title"],
                        "message"=>$data["message"],
                        "image"=> $data["image"],
                        "icon"=>$data["icon"]
                        );
                        $setting = $em->getRepository('AppBundle:Settings')->findOneBy(array());            
            $key=$setting->getFirebasekey();
            $message_image = $this->send_notification(null, $message,$key); 
           
            $this->addFlash('success', 'Operation has been done successfully ');
          
        }
        return $this->render('AppBundle:Home:notif_url.html.twig',array(
            "form"=>$form->createView()
        ));
    }
    public function faqAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(FaqType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:faq.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function apprefundpolicyAction() {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        return $this->render("AppBundle:Home:apprefundpolicy.html.twig", array("setting" => $setting));
    }
    public function refundpolicyAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(RefundType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:refundpolicy.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function edit_privacypolicyAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(PolicyType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:edit_privacypolicy.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function paymentAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(PaymentType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:payment.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function webadsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(WebAdsType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:webads.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function adsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(AdsType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:ads.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function websettingsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(WebSettingsType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($setting->getFile() != null) {
                $media = $setting->getLogo();
                if ($media == null) {
                    $media= new Media();
                    $media->setFile($setting->getFile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                    $setting->setLogo($media);
                    $em->flush();
                }else{
                    $media->setFile($setting->getFile());
                    $media->delete($this->container->getParameter('files_directory'));
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                }
            }
            if ($setting->getFavfile() != null) {
                $media = $setting->getFavicon();
                if ($media == null) {
                    $media= new Media();
                    $media->setFile($setting->getFavfile());
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                    $setting->setFavicon($media);
                    $em->flush();
                }else{
                    $media->setFile($setting->getFavfile());
                    $media->delete($this->container->getParameter('files_directory'));
                    $media->upload($this->container->getParameter('files_directory'));
                    $em->persist($media);
                    $em->flush();
                }

            }
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:websettings.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function settingsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array(), array());
        $form = $this->createForm(SettingsType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($setting->getFile() != null) {
                $media = $setting->getMedia();

                $media->setFile($setting->getFile());
                $media->delete($this->container->getParameter('files_directory'));
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
            }
            $em->persist($setting);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
        }
        return $this->render("AppBundle:Home:settings.html.twig", array("setting" => $setting, "form" => $form->createView()));
    } 
    public function indexAction(Request $request)
    {   


        $em=$this->getDoctrine()->getManager();
        $settings = $em->getRepository("AppBundle:Settings")->findOneBy(array());
        $support_count= $em->getRepository("AppBundle:Support")->count();

        $devices_count= $em->getRepository("AppBundle:Device")->count();

        $movies_count= $em->getRepository("AppBundle:Poster")->countMovies();

        $series_count= $em->getRepository("AppBundle:Poster")->countSeries();

        $channels_count= $em->getRepository("AppBundle:Channel")->count();

        $category_count= $em->getRepository("AppBundle:Category")->count();

        $comment_count= $em->getRepository("AppBundle:Comment")->count();

        $language_count= $em->getRepository("AppBundle:Language")->count();
        
        $version_count= $em->getRepository("AppBundle:Version")->count();
        $slide_count= $em->getRepository("AppBundle:Slide")->count();

        $users_count= $em->getRepository("UserBundle:User")->count();
        $actor_count= $em->getRepository("AppBundle:Actor")->count();
        $genre_count= $em->getRepository("AppBundle:Genre")->count();
        $country_count= $em->getRepository("AppBundle:Country")->count();

        $movie_downloads= $em->getRepository("AppBundle:Poster")->countMoviesDownloads();
        $movie_shares= $em->getRepository("AppBundle:Poster")->countMoviesShares();
        $movie_views= $em->getRepository("AppBundle:Poster")->countMoviesViews();

        $serie_downloads= $em->getRepository("AppBundle:Poster")->countSeriesDownloads();
        $serie_shares= $em->getRepository("AppBundle:Poster")->countSeriesShares();
        $serie_views= $em->getRepository("AppBundle:Poster")->countSeriesViews();

        $channel_shares= $em->getRepository("AppBundle:Channel")->countShares();
        $channel_views= $em->getRepository("AppBundle:Channel")->countViews();
        $comment_count= $em->getRepository("AppBundle:Comment")->count();
        $subscription_count= $em->getRepository("AppBundle:Subscription")->count();


        return $this->render('AppBundle:Home:index.html.twig',array(
            "slide_count"=>$slide_count,
            "support_count"=>$support_count,
            "devices_count"=>$devices_count,
            "movies_count"=>$movies_count,
            "series_count"=>$series_count,
            "channels_count"=>$channels_count,
            "category_count"=>$category_count,
            "language_count"=>$language_count,
            "genre_count"=>$genre_count,
            "country_count"=>$country_count,
            "actor_count"=>$actor_count,
            "version_count"=>$version_count,
            "movie_downloads"=>$movie_downloads,
            "movie_shares"=>$movie_shares,
            "movie_views"=>$movie_views,
            "serie_downloads"=>$serie_downloads,
            "serie_shares"=>$serie_shares,
            "serie_views"=>$serie_views,
            "channel_shares"=>$channel_shares,
            "channel_views"=>$channel_views,
            "comment_count"=>$comment_count,
            "subscription_count"=>$subscription_count
        ));
    }
    public function api_searchAction(Request $request, $token,$query) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }

        $em = $this->getDoctrine()->getManager();
        $repositoryChannel = $em->getRepository('AppBundle:Channel');
        $queryChannel = $repositoryChannel->createQueryBuilder('p')
                ->where("p.enabled = true","p.title like '%" . $query . "%' or p.tags like '%" . $query . "%'")
                ->addOrderBy('p.id', 'ASC')
                ->getQuery();         
            
        $channels = $queryChannel->getResult();

        $em = $this->getDoctrine()->getManager();
        $repositoryPoster = $em->getRepository('AppBundle:Poster');
        $queryPosters = $repositoryPoster->createQueryBuilder('p')
                ->leftJoin('p.seasons', 's')
                ->leftJoin('s.episodes', 'e')
                ->where("p.enabled = true","p.title like '%" . $query . "%' or e.title like '%" . $query . "%' or p.tags like '%" . $query . "%'")
                ->addOrderBy('p.id', 'ASC')
                ->getQuery();         
            
        $posters = $queryPosters->getResult();

        return $this->render('AppBundle:Home:api_search.html.php', array("channels"=>$channels,"posters"=>$posters));
    }

    public function api_mylistAction(Request $request, $token,$key,$id) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$id));
        $nombre = 30;
        $page = 0;
        $channels =array();

        if($user){

            if ($user->isEnabled()) {

                if ($key==sha1($user->getPassword())) {
                  

                  
                    $channels = $em->getRepository("AppBundle:Item")->findBy(array("poster"=>null,"user"=>$user), array("position" => "desc"));
                    $repository = $em->getRepository('AppBundle:Item');

                    $repo_query = $repository->createQueryBuilder('i');

                    $repo_query->leftJoin('i.poster', 'p');
                    $repo_query->where($repo_query->expr()->isNotNull('i.poster'));
                    $repo_query->andWhere("p.enabled = true");
                    $repo_query->andWhere("i.user =".$user->getId());

                    $repo_query->addOrderBy('i.position', "desc");
                    $repo_query->addOrderBy('p.id', 'ASC');

                    $query =  $repo_query->getQuery(); 
                    $query->setFirstResult($nombre * $page);
                    $query->setMaxResults($nombre);
                    $posters = $query->getResult();
                    
                    return $this->render('AppBundle:Home:api_mylist.html.php', array("posters"=>$posters,"channels"=>$channels));

                }
            }
        }
       return new Response("");
    }
    public function api_addlistAction(Request $request,$token)
    {

            $id =$request->request->get('id');
            $type =$request->request->get('type');
            $user =$request->request->get('user');
            $key =$request->request->get('key');
            if ($token != $this->container->getParameter('token_app')) {
                throw new NotFoundHttpException("Page not found");
            }
            $code = 500;
            $em=$this->getDoctrine()->getManager();
            $user_obj = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$user));

            if ($user_obj!=null){

                if ($type ==  "poster") {
                    $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($poster !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"poster" => $poster));
                        if ($item == null) {
                            
                            $last_item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"channel" =>null),array("position"=>"desc"));
                            $position=1;
                            if ($last_item!=null) {
                                $position=$last_item->getPosition()+1;
                            }
                            $code = 200;
                            $item = new Item();
                            $item->setPoster($poster);
                            $item->setUser($user_obj);
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
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"channel" => $channel));
                        if ($item == null) {
                            $last_item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"poster" =>null),array("position"=>"desc"));
                            $position=1;
                            if ($last_item!=null) {
                                $position=$last_item->getPosition()+1;
                            }


                            $code = 200;
                            $item = new Item();
                            $item->setChannel($channel);
                            $item->setUser($user_obj);
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
        
        return new Response($code);
    } 
    public function api_checklistAction(Request $request,$token)
    {
            $id =$request->request->get('id');
            $type =$request->request->get('type');
            $user =$request->request->get('user');
            $key =$request->request->get('key');
            if ($token != $this->container->getParameter('token_app')) {
                throw new NotFoundHttpException("Page not found");
            }
            $code = 500;
            $em=$this->getDoctrine()->getManager();
            $user_obj = $em->getRepository("UserBundle:User")->findOneBy(array("id"=>$user));

            if ($user_obj!=null){
                if ($type ==  "poster") {
                    $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($poster !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"poster" => $poster));
                        if ($item == null) {
                            $code = 202;
                        }else{
                            $code = 200;
                        }
                    }
                }
                if ($type ==  "channel") {
                    $channel = $em->getRepository("AppBundle:Channel")->findOneBy(array("id"=>$id,"enabled"=>true));
                    if ($channel !=null) {
                        $item = $em->getRepository("AppBundle:Item")->findOneBy(array("user"=>$user_obj,"channel" => $channel));
                        if ($item == null) {    
                            $code = 202;
                        }else{
                            $code = 200;
                        }
                    }
                }
            }
        
        return new Response($code);
    } 
    public function api_firstAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $em = $this->getDoctrine()->getManager();
        $slides = $em->getRepository("AppBundle:Slide")->findBy(array(), array("position" => "asc"));
        $genres = $em->getRepository("AppBundle:Genre")->findBy(array(), array("position" => "asc"));
        $popular = $em->getRepository("AppBundle:Poster")->findBy(array("enabled"=>true), array("views" => "desc"),20);
        $bestrated = $em->getRepository("AppBundle:Poster")->findBy(array("enabled"=>true), array("rating" => "desc"),10);

        $channels =  $em->getRepository("AppBundle:Channel")->findBy(array("featured"=>true),array("created"=>"desc"));

        $repository = $em->getRepository('AppBundle:Actor');

        $query = $repository->createQueryBuilder('A')
            ->select(array("A.id","A.name","A.type","A.born","A.height","A.bio","m.url as image","m.extension as extension","SUM(P.views) as test"))
            ->leftJoin('A.roles', 'G')
            ->leftJoin('G.poster', 'P')
            ->leftJoin('A.media', 'm')
            ->groupBy('A.id')
            ->orderBy('test',"DESC")
            ->setMaxResults(10)
            ->getQuery();

        $actors = $query->getResult();
        return $this->render('AppBundle:Home:api_all.html.php', array("bestrated"=>$bestrated,"popular"=>$popular,"actors"=>$actors,"genres"=>$genres,"channels"=>$channels,"slides"=>$slides));
    }
    public function api_deviceAction($tkn,$token){
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $code="200";
        $message="";
        $errors=array();
        $em = $this->getDoctrine()->getManager();
        $d=$em->getRepository('AppBundle:Device')->findOneBy(array("token"=>$tkn));
        if ($d==null) {
            $device = new Device();
            $device->setToken($tkn);
            $em->persist($device);
            $em->flush();
            $message="Deivce added";
        }else{
            $message="Deivce Exist";
        }

        $error=array(
            "code"=>$code,
            "message"=>$message,
            "values"=>$errors
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }

    



}