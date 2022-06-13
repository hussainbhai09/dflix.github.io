<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Poster;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Rate;
use AppBundle\Entity\Role;
use AppBundle\Entity\Actor;

use AppBundle\Entity\Source;
use AppBundle\Entity\Subtitle;
use MediaBundle\Entity\Media;
use AppBundle\Form\MovieType;
use AppBundle\Form\TrailerType;

use AppBundle\Form\SubtitleType;
use AppBundle\Form\RoleType;
use AppBundle\Form\SourceType;
use AppBundle\Form\EditMovieType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



class MovieController extends Controller

{
    public function api_by_idAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $poster=$em->getRepository("AppBundle:Poster")->find($id);
        if ($poster==null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render('AppBundle:Movie:api_one.html.php', array("poster" => $poster));
    }
    public function api_add_viewAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($poster == null) {
            throw new NotFoundHttpException("Page not found");
        }
        $poster->setViews($poster->getViews() + 1);
        $em->flush();
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($poster->getViews(), 'json');
        return new Response($jsonContent);
    }
    public function api_add_shareAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($poster == null) {
            throw new NotFoundHttpException("Page not found");
        }
        $poster->setShares($poster->getShares() + 1);
        $em->flush();
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($poster->getShares(), 'json');
        return new Response($jsonContent);
    }
    public function api_add_downloadAction(Request $request, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"enabled"=>true));
        if ($poster == null) {
            throw new NotFoundHttpException("Page not found");
        }
        $poster->setDownloads($poster->getDownloads() + 1);
        $em->flush();
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($poster->getDownloads(), 'json');
        return new Response($jsonContent);
    }
    public function api_poster_by_filtresAction(Request $request, $genre,$order,$page, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $nombre = 30;
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $repository = $em->getRepository('AppBundle:Poster');
        $dir = "DESC";
        if("title"==$order){
            $dir="ASC";
        }
        if($genre==0){
            $query = $repository->createQueryBuilder('p')
                ->where("p.enabled = true")
                ->addOrderBy('p.'.$order, $dir)
                ->addOrderBy('p.id', 'ASC')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();         
        }else{
            $query = $repository->createQueryBuilder('p')
                ->leftJoin('p.genres', 'g')
                ->where("p.enabled = true",'g.id = ' . $genre)
                ->addOrderBy('p.'.$order, $dir)
                ->addOrderBy('p.id', 'ASC')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();         
        }  
        $posters_list = $query->getResult();
        return $this->render('AppBundle:Movie:api_all.html.php', array("posters_list" => $posters_list));
    }
    public function api_by_filtresAction(Request $request, $genre,$order,$page, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $nombre = 30;
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $repository = $em->getRepository('AppBundle:Poster');
        $dir = "DESC";
        if("title"==$order){
            $dir="ASC";
        }
        if($genre==0){
            $query = $repository->createQueryBuilder('p')
                ->where("p.enabled = true","p.type like 'movie' ")
                ->addOrderBy('p.'.$order, $dir)
                ->addOrderBy('p.id', 'ASC')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();
            }else{
                 $query = $repository->createQueryBuilder('p')
                ->leftJoin('p.genres', 'g')
                ->where("p.enabled = true","p.type like 'movie' ",'g.id = ' . $genre)
                ->addOrderBy('p.'.$order, $dir)
                ->addOrderBy('p.id', 'ASC')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();         
            }
        $posters_list = $query->getResult();
        return $this->render('AppBundle:Movie:api_all.html.php', array("posters_list" => $posters_list));
    }
    public function api_randomAction(Request $request, $genres, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $nombre = 30;
        $em = $this->getDoctrine()->getManager();
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $repository = $em->getRepository('AppBundle:Poster');
        $query = $repository->createQueryBuilder('p')
            ->leftJoin('p.genres', 'g')
            ->where("p.enabled = true","p.type like 'movie' ",'g.id in (' . $genres . ')')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults($nombre)
            ->getQuery();
        $posters_list = $query->getResult();
        return $this->render('AppBundle:Movie:api_all.html.php', array("posters_list" => $posters_list));
    }

    public function api_by_actorAction(Request $request, $id, $token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
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
        $posters_list = $query->getResult();
        return $this->render('AppBundle:Movie:api_all.html.php', array("posters_list" => $posters_list));
    }
    
    public function indexAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $q = " ";
        if ($request->query->has("q") and $request->query->get("q") != "") {
            $q .= " AND  p.title like '%" . $request->query->get("q") . "%'";
        }

        $dql = "SELECT p FROM AppBundle:Poster p  WHERE  p.type  like 'movie' " . $q . " ORDER BY p.created desc ";
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $movies = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            16
        );
        $movies_count = $em->getRepository('AppBundle:Poster')->countMovies();
        return $this->render('AppBundle:Movie:index.html.twig', array("movies_count" => $movies_count, "movies" => $movies));
    }
    function get_image_mime_type($image_path)
    {
        $mimes  = array(
            IMAGETYPE_GIF => "image/gif",
            IMAGETYPE_JPEG => "image/jpg",
            IMAGETYPE_PNG => "image/png",
            IMAGETYPE_SWF => "image/swf",
            IMAGETYPE_PSD => "image/psd",
            IMAGETYPE_BMP => "image/bmp",
            IMAGETYPE_TIFF_II => "image/tiff",
            IMAGETYPE_TIFF_MM => "image/tiff",
            IMAGETYPE_JPC => "image/jpc",
            IMAGETYPE_JP2 => "image/jp2",
            IMAGETYPE_JPX => "image/jpx",
            IMAGETYPE_JB2 => "image/jb2",
            IMAGETYPE_SWC => "image/swc",
            IMAGETYPE_IFF => "image/iff",
            IMAGETYPE_WBMP => "image/wbmp",
            IMAGETYPE_XBM => "image/xbm",
            IMAGETYPE_ICO => "image/ico");

        if (($image_type = exif_imagetype($image_path))
            && (array_key_exists($image_type ,$mimes)))
        {
            return $mimes[$image_type];
        }
        else
        {
            return FALSE;
        }
    }
   function get_image_ext_type($image_path)
    {
        $mimes  = array(
            IMAGETYPE_GIF => "gif",
            IMAGETYPE_JPEG => "jpg",
            IMAGETYPE_PNG => "png",
            IMAGETYPE_SWF => "swf",
            IMAGETYPE_PSD => "psd",
            IMAGETYPE_BMP => "bmp",
            IMAGETYPE_TIFF_II => "tiff",
            IMAGETYPE_TIFF_MM => "tiff",
            IMAGETYPE_JPC => "jpc",
            IMAGETYPE_JP2 => "jp2",
            IMAGETYPE_JPX => "jpx",
            IMAGETYPE_JB2 => "jb2",
            IMAGETYPE_SWC => "swc",
            IMAGETYPE_IFF => "iff",
            IMAGETYPE_WBMP => "wbmp",
            IMAGETYPE_XBM => "xbm",
            IMAGETYPE_ICO => "ico");

        if (($image_type = exif_imagetype($image_path))
            && (array_key_exists($image_type ,$mimes)))
        {
            return $mimes[$image_type];
        }
        else
        {
            return FALSE;
        }
    }

    public function import_trailerAction(Request $request,$id,$movie){
        $em = $this->getDoctrine()->getManager();
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$movie));
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $curl_trailer = curl_init("https://api.themoviedb.org/3/movie/".$id."/videos?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
            curl_setopt($curl_trailer, CURLOPT_FAILONERROR, true);
            curl_setopt($curl_trailer, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_trailer, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_trailer, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_trailer, CURLOPT_SSL_VERIFYPEER, false);  
            $result_trailer = curl_exec($curl_trailer);
            $trailer_infos =  json_decode($result_trailer);
            $selected_video = null;

            foreach ($trailer_infos->results as $key => $video) {
               if ($video->type == "Trailer" and $video->site == "YouTube") {
                        $selected_video=$video;
               }
            }

            if ($selected_video !=null) {
                $source = new Source();
                $source->setUrl("https://www.youtube.com/watch?v=".$selected_video->key);
                $source->setType("youtube");
                $em->persist($source);
                $em->flush();

                $poster->setTrailer($source);
                $em->flush();
             }
         }
        
        return $this->render("AppBundle:Movie:import_trailer.html.twig",array("setting"=>$setting,"poster"=>$poster,"form"=>$form->createView()));
    }

    public function import_castsAction(Request $request,$id,$movie){
        $em = $this->getDoctrine()->getManager();
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$movie));
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $curl_actors = curl_init("https://api.themoviedb.org/3/movie/".$id."/casts?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
            curl_setopt($curl_actors, CURLOPT_FAILONERROR, true);
            curl_setopt($curl_actors, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_actors, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_actors, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_actors, CURLOPT_SSL_VERIFYPEER, false);  
            $result_actors = curl_exec($curl_actors);
            $actors_poster =  json_decode($result_actors);

            $max=0;
            $role=$em->getRepository('AppBundle:Role')->findOneBy(array("poster"=>$poster),array("position"=>"desc"));
            if ($role!=null) {
                $max = $role->getPosition();
            }
            $actors_exist = $em->getRepository('AppBundle:Actor')->findAll();
            $exist_actor = false;
            $get_actor = null;
            $count_added = 0;
            foreach ($actors_poster->cast as $key => $actor) {
                foreach ($actors_exist as $keyJ => $actor_exist) {
                    if (strtoupper($actor->name) == strtoupper($actor_exist->getName())) {
                         $exist_actor=  true;
                         $get_actor = $actor_exist;
                    }
                }
                if($exist_actor){
                    $role = new Role();
                    $role->setActor($get_actor);
                    $role->setPoster($poster);
                    $role->setRole($actor->character);
                    $max++;
                    $role->setPosition($max);
                    $em->persist($role);
                    $em->flush();                     
                }else{
                    if ($actor->profile_path != null) {
                        if ($count_added<15){
                            $curl_persone = curl_init("https://api.themoviedb.org/3/person/".$actor->id."?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
                            curl_setopt($curl_persone, CURLOPT_FAILONERROR, true);
                            curl_setopt($curl_persone, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($curl_persone, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl_persone, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($curl_persone, CURLOPT_SSL_VERIFYPEER, false);  
                            $result_persone = curl_exec($curl_persone);
                            $persone_infos =  json_decode($result_persone);

                            $url_actor =  "https://image.tmdb.org/t/p/w400".$actor->profile_path;
                                # code...
                            
                            $actorfileName = md5(uniqid());
                            $actorfileType = "image/jpg";
                            $actorfileExt = "jpg";
                            $actorfullName = $actorfileName.".".$actorfileExt;
                            $actor_uploadTo = $this->container->getParameter('files_directory').$actorfileExt."/".$actorfullName;
                            file_put_contents($actor_uploadTo, file_get_contents($url_actor)); 

                            $postermedia= new Media();
                            $postermedia->setType($actorfileType);
                            $postermedia->setExtension($actorfileExt);
                            $postermedia->setUrl($actorfullName);
                            $postermedia->setTitre($actor->name);
                            $em->persist($postermedia);
                            $em->flush();

                            $newactor= new Actor();
                            $newactor->setName($actor->name);
                            if($persone_infos != null){
                                $newactor->setBio($persone_infos->biography);
                                $newactor->setBorn($persone_infos->birthday.", ".$persone_infos->place_of_birth);
                                $newactor->setType($persone_infos->known_for_department);
                                $newactor->setHeight("");
                            }else{
                                $newactor->setBio("");
                                $newactor->setBorn("");
                                $newactor->setType("");
                                $newactor->setHeight("");
                            }
                            $newactor->setMedia($postermedia);
                            $em->persist($newactor);
                            $em->flush();

                            $role = new Role();
                            $role->setActor($newactor);
                            $role->setPoster($poster);
                            $role->setRole($actor->character);
                            $max++;
                            $role->setPosition($max);
                            $em->persist($role);
                            $em->flush();
                            $count_added++;   

                        }
                    }
                }
                $exist_actor = false;
            } 

             $this->addFlash('success', 'Operation has been done successfully');
             return $this->redirect($this->generateUrl('app_movie_import_trailer',array("id"=>$id,"movie"=>$poster->getId())));

        }
        return $this->render("AppBundle:Movie:import_casts.html.twig",array("setting"=>$setting,"poster"=>$poster,"form"=>$form->createView()));

    }
    public function import_keywordsAction(Request $request,$id,$movie){

        $em = $this->getDoctrine()->getManager();
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$movie));
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $curl_keywords = curl_init("https://api.themoviedb.org/3/movie/".$id."/keywords?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
            curl_setopt($curl_keywords, CURLOPT_FAILONERROR, true);
            curl_setopt($curl_keywords, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_keywords, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_keywords, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_keywords, CURLOPT_SSL_VERIFYPEER, false);  

            $result_keywords = curl_exec($curl_keywords);
            $keywords_poster =  json_decode($result_keywords);
            $keywords = "";
            foreach ($keywords_poster->keywords as $key => $keyword) {
                if ($key == 0) {
                    $keywords.=$keyword->name;
                }else{
                    $keywords.=",".$keyword->name;
                }
            }
           $poster->setTags($keywords);
           $em->flush();
             $this->addFlash('success', 'Operation has been done successfully');
             return $this->redirect($this->generateUrl('app_movie_import_casts',array("id"=>$id,"movie"=>$poster->getId())));

        }
       return $this->render("AppBundle:Movie:import_keywords.html.twig",array("setting"=>$setting,"poster"=>$poster,"form"=>$form->createView()));

    }
    public function importAction(Request $request){

        $em=$this->getDoctrine()->getManager();
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $id = $data["id"];
            // get Movies details 
            $curl = curl_init("https://api.themoviedb.org/3/movie/".$id."?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
            curl_setopt($curl, CURLOPT_FAILONERROR, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
            $result = curl_exec($curl);
            $detail_poster =  json_decode($result);
            $title = $detail_poster->title;
            $poster_path = $detail_poster->poster_path;
            $cover_path = $detail_poster->backdrop_path;

            $duration = $this->toTime($detail_poster->runtime);
            $description = $detail_poster->overview;
            $imdb = $detail_poster->vote_average;
            $genres = $detail_poster->genres;
            $year = substr($detail_poster->release_date, 0, 4);

            // get Movies keywords 

 
            // set Movies infos 

            $movie= new Poster();
            $movie->setTitle($title);
            $movie->setDuration($duration);
            $movie->setPlayas(1);
            $movie->setDownloadas(1);
            $movie->setType("movie");
            $movie->setTags("");
            $movie->setRating("0");
            $movie->setImdb($imdb);
            $movie->setDescription($description);
            $movie->setDownloads(0);
            $movie->setShares(0);
            $movie->setViews(0);
            $movie->setComment(true);
            $movie->setEnabled(true);
            $movie->setYear($year);
            // get poster Movies image 

            $url =  "https://image.tmdb.org/t/p/original".$poster_path;
            $fileName = md5(uniqid());
            $fileType = "image/jpg";
            $fileExt = "jpg";
            $fullName = $fileName.".".$fileExt;

            $uploadTo = $this->container->getParameter('files_directory').$fileExt."/".$fullName;

            file_put_contents($uploadTo, file_get_contents($url)); 

            $moviemedia= new Media();
            $moviemedia->setType($fileType);
            $moviemedia->setExtension($fileExt);
            $moviemedia->setUrl($fullName);
            $moviemedia->setTitre($movie->getTitle());
            $em->persist($moviemedia);
            $em->flush();
            $movie->setPoster($moviemedia);

            // get cover Movies image 
            if ($cover_path != null) {
                $url_cover =  "https://image.tmdb.org/t/p/original".$cover_path;
                $fileCoverName = md5(uniqid());
                $fileCoverType = "image/jpg";
                $fileCoverExt = "jpg";
                $fullCoverName = $fileCoverName.".".$fileCoverExt;

                $uploadTo = $this->container->getParameter('files_directory').$fileCoverExt."/".$fullCoverName;

                file_put_contents($uploadTo, file_get_contents($url_cover)); 

                $cover_moviemedia= new Media();
                $cover_moviemedia->setType($fileCoverType);
                $cover_moviemedia->setExtension($fileCoverExt);
                $cover_moviemedia->setUrl($fullCoverName);
                $cover_moviemedia->setTitre($movie->getTitle());
                $em->persist($cover_moviemedia);
                $em->flush();
                $movie->setCover($cover_moviemedia);
            }

            $genrs_exist = $em->getRepository('AppBundle:Genre')->findAll();
            $exist = false;
            $get_geren = null;
            foreach ($genres as $key => $genre) {
                foreach ($genrs_exist as $key_exist => $genre_exist) {
                    if (strtoupper($genre->name) == strtoupper($genre_exist->getTitle())) {
                         $exist=  true;
                         $get_geren = $genre_exist;
                    }
                }
                if($exist){
                     $movie->addGenre($get_geren);
                }else{
                    $last_genre = $em->getRepository('AppBundle:Genre')->findOneBy(array(),array("position"=>"desc"));
                    $new_position = ($last_genre == null)? 0 :$last_genre->getPosition()+1;
                    $newgenre= new Genre();
                    $newgenre->setTitle($genre->name);
                    $newgenre->setPosition($new_position);
                    $em->persist($newgenre);
                    $em->flush();
                    $movie->addGenre($newgenre);
                }
                $exist = false;
            }
           

            $em->persist($movie);
            $em->flush();
            
             $this->addFlash('success', 'Operation has been done successfully');
             return $this->redirect($this->generateUrl('app_movie_import_keywords',array("id"=>$id,"movie"=>$movie->getId())));

        }
       return $this->render("AppBundle:Movie:import.html.twig",array("setting"=>$setting,"form"=>$form->createView()));
    }
    public function toTime($final_time_saving){
        $hours = floor($final_time_saving / 60);
        $minutes = $final_time_saving % 60;
        $time = "";
        if ($hours!=0) {
            $time =$hours."h ";
        }
        $time.=$minutes."min";
        return $time;
    }
    public function addAction(Request $request)
    {
        $trailer_select=1;
        $source_select=1;
        $movie= new Poster();
        $form = $this->createForm(MovieType::class,$movie);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if( $movie->getFileposter()!=null or (isset($_POST["image_url"]) and $_POST["image_url"]!=null and $_POST["image_url"]!="" and strpos($_POST["image_url"], 'http') === 0)){
                    $choices = array(
                        1 => "youtube",
                        2 => "m3u8",
                        3 => "mov",
                        4 => "mp4",
                        6 => "mkv",
                        7 => "webm",
                        8 => "embed",
                        5 => "file"
                    );
                
                    $movie->setType("movie");
                    $movie->setRating("0");
                    if( $movie->getFileposter()!=null){
                        $media= new Media();
                        $media->setFile($movie->getFileposter());
                        $media->upload($this->container->getParameter('files_directory'));
                        $em->persist($media);
                        $em->flush();
                        $movie->setPoster($media);
                    }else{
                        if (isset($_POST["image_url"]) and $_POST["image_url"]!=null and $_POST["image_url"]!="" and strpos($_POST["image_url"], 'http') === 0) {
                            $url =  $_POST["image_url"];
                            $fileName = md5(uniqid());
                            $fileType = $this->get_image_mime_type($url);
                            $fileExt = $this->get_image_ext_type($url);
                            $fullName = $fileName.".".$fileExt;

                            $uploadTo = $this->container->getParameter('files_directory').$fileExt."/".$fullName;

                            file_put_contents($uploadTo, file_get_contents($url)); 

                            $moviemedia= new Media();
                            $moviemedia->setType($fileType);
                            $moviemedia->setExtension($fileExt);
                            $moviemedia->setUrl($fullName);
                            $moviemedia->setTitre($movie->getTitle());
                            $em->persist($moviemedia);
                            $em->flush();
                            $movie->setPoster($moviemedia);
                        }
                    }
                    if($movie->getFilecover()!=null ){
                        $mediacover= new Media();
                        $mediacover->setFile($movie->getFilecover());
                        $mediacover->upload($this->container->getParameter('files_directory'));
                        $em->persist($mediacover);
                        $em->flush();
                        $movie->setCover($mediacover);
                    }

                    if(strlen($movie->getTrailerurl())>1 ){
                        $trailer = new  Source();
                        $trailer->setType("youtube");
                        $trailer->setUrl($movie->getTrailerurl());
                        $em->persist($trailer);
                        $em->flush();
                        $movie->setTrailer($trailer);
                    }
                    


                    $em->persist($movie);
                    $em->flush();

                    if ($movie->getSourcetype()==5) {
                        if ($movie->getSourcefile()!=null ){
                            $mediasource= new Media();
                            $mediasource->setFile($movie->getSourcefile());
                            $mediasource->upload($this->container->getParameter('files_directory'));
                            $em->persist($mediasource);
                            $em->flush();

                            $source = new  Source();
                            $source->setType($choices[$movie->getSourcetype()]);
                            $source->setMedia($mediasource);
                            $source->setPoster($movie);
                            $em->persist($source);
                            $em->flush();  
                        }
                    }else{
                        if(strlen($movie->getSourceurl())>1 ){
                            $source = new  Source();
                            $source->setType($choices[$movie->getSourcetype()]);
                            $source->setUrl($movie->getSourceurl());
                            $source->setPoster($movie);
                            $em->persist($source);
                            $em->flush();
                        }
                    }
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_movie_index'));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('fileposter')->addError($error);
                }
       }
       return $this->render("AppBundle:Movie:add.html.twig",array("trailer_select"=> $trailer_select,"source_select"=> $source_select,"form"=>$form->createView()));
    }

    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $movie = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if($movie==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $slide = $em->getRepository("AppBundle:Slide")->findOneBy(array("poster"=>$movie));

            if ($slide!=null) {
                $media_slide = $slide->getMedia();
                $em->remove($slide);
                $em->flush();

                if ($media_slide != null) {
                    $media_slide->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_slide);
                    $em->flush();
                }
                $slides = $em->getRepository('AppBundle:Slide')->findBy(array(), array("position" => "asc"));

                $p = 1;
                foreach ($slides as $key => $value) {
                    $value->setPosition($p);
                    $p++;
                }
                $em->flush();
            }
            foreach ($movie->getSources() as $key => $source) {
                $media_source = $source->getMedia();

                $em->remove($source);
                $em->flush();

                if ($media_source!=null) {
                    $media_source->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_source);
                    $em->flush();
                }
            }
            foreach ($movie->getSubtitles() as $key => $subtitle) {
                $media_subtitle = $subtitle->getMedia();
                
                $em->remove($subtitle);
                $em->flush();

                if ($media_subtitle!=null) {
                    $media_subtitle->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_subtitle);
                    $em->flush();
                }
            }

            $media_cover = $movie->getCover();
            $media_poster = $movie->getPoster();

            $em->remove($movie);
            $em->flush();

            if ($media_cover!=null) {
                $media_cover->delete($this->container->getParameter('files_directory'));
                $em->remove($media_cover);
                $em->flush();
            }

            if ($media_poster!=null) {
                $media_poster->delete($this->container->getParameter('files_directory'));
                $em->remove($media_poster);
                $em->flush();
            }

            $trailer = $movie->getTrailer();

            if ($trailer!=null) {

                $media_trailer = $trailer->getMedia();

                $em->remove($trailer);
                $em->flush();

                if ($media_trailer!=null) {
                    $media_trailer->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_trailer);
                    $em->flush();
                }
            }

           $this->addFlash('success', 'Operation has been done successfully');
           return $this->redirect($this->generateUrl('app_movie_index'));
        }
        return $this->render('AppBundle:Movie:delete.html.twig',array("form"=>$form->createView()));
    }
    public function subtitlesAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $movie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if ($movie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render("AppBundle:Movie:subtitles.html.twig",array("movie"=>$movie));
    }
    public function trailerAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $movie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if ($movie==null) {
            throw new NotFoundHttpException("Page not found");
        }

        $source = new Source();
        $trailer_form = $this->createForm(TrailerType::class,$source);
        $trailer_form->handleRequest($request);
        if ($trailer_form->isSubmitted() && $trailer_form->isValid()) {
            $choices = array(
                1 => "youtube",
                2 => "m3u8",
                3 => "mov",
                4 => "mp4",
                6 => "mkv",
                7 => "webm",
                8 => "embed",
                5 => "file"
            );
            if ($source->getType()==5) {
                if ($source->getFile()!=null ){
                    $sourcemedia= new Media();
                    $sourcemedia->setFile($source->getFile());
                    $sourcemedia->upload($this->container->getParameter('files_directory'));
                    $em->persist($sourcemedia);
                    $em->flush();

                    $source->setType($choices[$source->getType()]);
                    $source->setMedia($sourcemedia);
                    $em->persist($source);
                    $em->flush(); 

                    $movie->setTrailer($source);
                    $em->flush();
                }
            }else{
                if(strlen($source->getUrl())>1 ){
                    $source->setType($choices[$source->getType()]);
                    $em->persist($source);
                    $em->flush();
                    $movie->setTrailer($source);
                    $em->flush();
                }
            }
        }
        return $this->render("AppBundle:Movie:trailer.html.twig",array("trailer_form"=>$trailer_form->createView(),"movie"=>$movie));
    }
    public function castAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $movie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if ($movie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $dql        = "SELECT r FROM AppBundle:Role r  WHERE r.poster = ". $id ." ORDER BY r.position desc ";
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
            11
        );

        $role = new Role();
        $role_form = $this->createForm(RoleType::class,$role);
        $role_form->handleRequest($request);
        if ($role_form->isSubmitted() && $role_form->isValid()) {
                $max=0;
                $roles=$em->getRepository('AppBundle:Role')->findBy(array("poster"=>$movie));
                foreach ($roles as $key => $value) {
                    if ($value->getPosition()>$max) {
                        $max=$value->getPosition();
                    }
                }
                $role->setPosition($max+1);
                $role->setPoster($movie);
                $em->persist($role);
                $em->flush();  
        }
        return $this->render("AppBundle:Movie:cast.html.twig",array("role_form"=>$role_form->createView(),'pagination' => $pagination,"movie"=>$movie));
    }
    public function sourcesAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $movie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if ($movie==null) {
            throw new NotFoundHttpException("Page not found");
        }

        return $this->render("AppBundle:Movie:sources.html.twig",array("movie"=>$movie));
    }
    public function commentsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $movie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if ($movie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $em= $this->getDoctrine()->getManager();
        $dql        = "SELECT c FROM AppBundle:Comment c  WHERE c.poster = ". $id ." ORDER BY c.created desc ";
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
            10
        );
       $count=$em->getRepository('AppBundle:Comment')->countByPoster($movie->getId());
        
        return $this->render('AppBundle:Movie:comments.html.twig',
            array(
                'pagination' => $pagination,
                'movie' => $movie,
                'count' => $count,
            )
        );
    }
    public function ratingsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $movie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if ($movie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $rates_1 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$movie,"value"=>1));
        $rates_2 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$movie,"value"=>2));
        $rates_3 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$movie,"value"=>3));
        $rates_4 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$movie,"value"=>4));
        $rates_5 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$movie,"value"=>5));
        $rates = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$movie));


        $ratings["rate_1"]=sizeof($rates_1);
        $ratings["rate_2"]=sizeof($rates_2);
        $ratings["rate_3"]=sizeof($rates_3);
        $ratings["rate_4"]=sizeof($rates_4);
        $ratings["rate_5"]=sizeof($rates_5);


        $t = sizeof($rates_1) + sizeof($rates_2) +sizeof($rates_3)+ sizeof($rates_4) + sizeof($rates_5);
        if ($t == 0) {
            $t=1;
        }
        $values["rate_1"]=(sizeof($rates_1)*100)/$t;
        $values["rate_2"]=(sizeof($rates_2)*100)/$t;
        $values["rate_3"]=(sizeof($rates_3)*100)/$t;
        $values["rate_4"]=(sizeof($rates_4)*100)/$t;
        $values["rate_5"]=(sizeof($rates_5)*100)/$t;

        $total=0;
        $count=0;
        foreach ($rates as $key => $r) {
           $total+=$r->getValue();
           $count++;
        }
        $v=0;
        if ($count != 0) {
            $v=$total/$count;
        }
        $rating=$v;
        $count=$em->getRepository('AppBundle:Rate')->countByPoster($movie->getId());
        
        $em= $this->getDoctrine()->getManager();
        $dql        = "SELECT c FROM AppBundle:Rate c  WHERE c.poster = ". $id ." ORDER BY c.created desc ";
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
            10
        );
        return $this->render("AppBundle:Movie:ratings.html.twig", array("pagination"=>$pagination,"count"=>$count,"rating"=>$rating,"ratings"=>$ratings,"values"=>$values,"movie" => $movie));

    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $movie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"movie"));
        if ($movie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(EditMovieType::class,$movie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $movie->getFilecover()!=null ){
                $media_cover= new Media();
                $media_cover_old=$movie->getCover();
                $media_cover->setFile($movie->getFilecover());
                $media_cover->upload($this->container->getParameter('files_directory'));
                $em->persist($media_cover);
                $em->flush();

                $movie->setCover($media_cover);
                if ($media_cover_old!=null) {
                    $media_cover_old->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_cover_old);
                    $em->flush();
                }
            }
            if( $movie->getFileposter()!=null ){
                $media_poster= new Media();
                $media_poster_old=$movie->getPoster();
                $media_poster->setFile($movie->getFileposter());
                $media_poster->upload($this->container->getParameter('files_directory'));
                $em->persist($media_poster);
                $em->flush();
                
                $movie->setPoster($media_poster);
                $media_poster_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_poster_old);
                $em->flush();
            }
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_movie_index'));
        }
        return $this->render("AppBundle:Movie:edit.html.twig",array("movie"=>$movie,"form"=>$form->createView()));
    }
    public function api_add_rateAction(Request $request,$token) {
        if ($token != $this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");
        }
        $user = $request->get("user");
        $poster = $request->get("poster");
        $key = $request->get("key");
        $value = $request->get("value");

        $em = $this->getDoctrine()->getManager();
        $poster_obj = $em->getRepository('AppBundle:Poster')->find($poster);
        $user_obj = $em->getRepository("UserBundle:User")->find($user);

        $code = "200";
        $message = "";
        $errors = array();
        if ($user_obj != null and $poster_obj != null) {
            if (sha1($user_obj->getPassword()) == $key) {
                $rate = $em->getRepository('AppBundle:Rate')->findOneBy(array("user" => $user_obj, "poster" => $poster_obj));
                if ($rate == null) {
                    $rate_obj = new Rate();
                    $rate_obj->setValue($value);
                    $rate_obj->setPoster($poster_obj);
                    $rate_obj->setUser($user_obj);
                    $em->persist($rate_obj);
                    $em->flush();
                    $message = "Your Ratting has been added";
                } else {
                    $rate->setValue($value);
                    $em->flush();
                    $message = "Your Ratting has been edit"; 
                }
                $rates = $em->getRepository('AppBundle:Rate')->findBy(array("poster" => $poster_obj));

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
                
                $poster_obj->setRating($v2);
                $em->flush();
            }else {
                $code = "500";
                $message = "Sorry, your rate could not be added at this time";

            }
        } else {
            $code = "500";
            $message = "Sorry, your rate could not be added at this time";
        }
        $error = array(
            "code" => $code,
            "message" => $message,
            "values" => $errors,
        );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($error, 'json');
        return new Response($jsonContent);
    }
    public function shareAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $poster = $em->getRepository("AppBundle:Poster")->find($id);
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());
        if ($poster == null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render("AppBundle:Movie:share.html.twig", array("poster" => $poster, "setting" => $setting));
    }
}
?>