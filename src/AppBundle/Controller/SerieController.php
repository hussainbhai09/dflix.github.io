<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Poster;
use AppBundle\Entity\Rate;
use AppBundle\Entity\Role;
use AppBundle\Entity\Source;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Actor;
use AppBundle\Entity\Episode;

use AppBundle\Entity\Season;
use AppBundle\Entity\Subtitle;
use MediaBundle\Entity\Media;
use AppBundle\Form\SerieType;
use AppBundle\Form\SeasonType;
use AppBundle\Form\TrailerType;

use AppBundle\Form\RoleType;
use AppBundle\Form\SourceType;
use AppBundle\Form\EditSerieType;
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
class SerieController extends Controller
{

     // get Serie Tv keywords 
    public function import_castsAction(Request $request,$id,$serie){
        $em = $this->getDoctrine()->getManager();
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$serie));
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $curl_actors = curl_init("https://api.themoviedb.org/3/tv/".$id."/credits?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
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

                            $url_actor =  "https://image.tmdb.org/t/p/original".$actor->profile_path;
                                # code...
                            
                            $actorfileName = md5(uniqid());
                            $actorfileType = "image/jpg";
                            $actorfileExt ="jpg";
                            $actorfullName = $actorfileName.".".$actorfileExt;
                            $actor_uploadTo = $this->container->getParameter('files_directory').$actorfileExt."/".$actorfullName;
                            file_put_contents($actor_uploadTo, file_get_contents($url_actor)); 

                            $actormedia= new Media();
                            $actormedia->setType($actorfileType);
                            $actormedia->setExtension($actorfileExt);
                            $actormedia->setUrl($actorfullName);
                            $actormedia->setTitre($actor->name);
                            $em->persist($actormedia);
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
                            $newactor->setMedia($actormedia);
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
             return $this->redirect($this->generateUrl('app_serie_import_trailer',array("id"=>$id,"serie"=>$poster->getId())));

        }
       return $this->render("AppBundle:Serie:import_casts.html.twig",array("setting"=>$setting,"poster"=>$poster,"form"=>$form->createView()));

    }
    public function import_episodesAction(Request $request,$id,$serie,$season){
        $em = $this->getDoctrine()->getManager();
               $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$serie));
        $seasonObj = $em->getRepository("AppBundle:Season")->findOneBy(array("id"=>$season));
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                $curl_season = curl_init("https://api.themoviedb.org/3/tv/".$id."/season/".$seasonObj->getPosition()."?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
                curl_setopt($curl_season, CURLOPT_FAILONERROR, true);
                curl_setopt($curl_season, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl_season, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_season, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl_season, CURLOPT_SSL_VERIFYPEER, false);
                

                $result_season = curl_exec($curl_season);
                $season_infos =  json_decode($result_season);
                $max_episode = 0;
                foreach ($season_infos->episodes as $key => $ep_new) {

                    $episode = new Episode();
                    if ($ep_new->still_path != null) {
                        
                        $url_episode =  "https://image.tmdb.org/t/p/original".$ep_new->still_path;
                        
                        $episodefileName = md5(uniqid());
                        $episodefileType = "image/jpg";
                        $episodefileExt = "jpg";
                        $episodefullName = $episodefileName.".".$episodefileExt;
                        $episode_uploadTo = $this->container->getParameter('files_directory').$episodefileExt."/".$episodefullName;
                        file_put_contents($episode_uploadTo, file_get_contents($url_episode)); 

                        $episode_media= new Media();
                        $episode_media->setType($episodefileType);
                        $episode_media->setExtension($episodefileExt);
                        $episode_media->setUrl($episodefullName);
                        $episode_media->setTitre($ep_new->name);
                        $em->persist($episode_media);
                        $em->flush();
                        $episode->setMedia($episode_media);
                    }

                    $episode->setTitle($ep_new->name);
                    $episode->setDescription($ep_new->overview);
                    $episode->setPlayas(1);
                    $episode->setDownloadas(1);
                    $episode->setPosition($max_episode);
                    $episode->setDownloads(0);
                    $episode->setViews(0);
                    $episode->setEnabled(true);
                    $episode->setSeason($seasonObj);
                    $max_episode++;
                    $em->persist($episode);
                    $em->flush();  
                }
             if ($poster->getSeasons()[$seasonObj->getPosition()+1] != null ) {
                   return $this->redirect($this->generateUrl('app_serie_import_episodes',array("id"=>$id,"serie"=>$poster->getId(),"season"=>$poster->getSeasons()[$seasonObj->getPosition()+1]->getId())));
             }else{
                 return $this->redirect($this->generateUrl('app_serie_import_done',array("id"=>$id,"serie"=>$poster->getId())));
             }
        }
       return $this->render("AppBundle:Serie:import_episodes.html.twig",array("setting"=>$setting,"poster"=>$poster,"season"=>$seasonObj,"form"=>$form->createView()));

    }
    public function import_trailerAction(Request $request,$id,$serie){
        $em = $this->getDoctrine()->getManager();
                $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$serie));
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $curl_trailer = curl_init("https://api.themoviedb.org/3/tv/".$id."/videos?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
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
             $this->addFlash('success', 'Operation has been done successfully');
             if ($poster->getSeasons()[0] != null ) {
                   return $this->redirect($this->generateUrl('app_serie_import_episodes',array("id"=>$id,"serie"=>$poster->getId(),"season"=>$poster->getSeasons()[0]->getId())));
             }else{
                 return $this->redirect($this->generateUrl('app_serie_import_done',array("id"=>$id,"serie"=>$poster->getId())));
             }

        }
       return $this->render("AppBundle:Serie:import_trailer.html.twig",array("setting"=>$setting,"poster"=>$poster,"form"=>$form->createView()));

    }
    public function import_doneAction(Request $request,$id,$serie){
        $em = $this->getDoctrine()->getManager();
                $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$serie));
        return $this->render("AppBundle:Serie:import_done.html.twig",array("setting"=>$setting,"poster"=>$poster));

    }
    
    public function import_keywordsAction(Request $request,$id,$serie){

        $em = $this->getDoctrine()->getManager();
        $poster = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$serie));
        $setting = $em->getRepository("AppBundle:Settings")->findOneBy(array());

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $curl_keywords = curl_init("https://api.themoviedb.org/3/tv/".$id."/keywords?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
            curl_setopt($curl_keywords, CURLOPT_FAILONERROR, true);
            curl_setopt($curl_keywords, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_keywords, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_keywords, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_keywords, CURLOPT_SSL_VERIFYPEER, false);  

            $result_keywords = curl_exec($curl_keywords);
            $keywords_poster =  json_decode($result_keywords);
            $keywords = "";
            foreach ($keywords_poster->results as $key => $keyword) {
                if ($key == 0) {
                    $keywords.=$keyword->name;
                }else{
                    $keywords.=",".$keyword->name;
                }
            }
           $poster->setTags($keywords);
           $em->flush();
             $this->addFlash('success', 'Operation has been done successfully');
             return $this->redirect($this->generateUrl('app_serie_import_casts',array("id"=>$id,"serie"=>$poster->getId())));

        }
       return $this->render("AppBundle:Serie:import_keywords.html.twig",array("setting"=>$setting,"poster"=>$poster,"form"=>$form->createView()));

    }
   
            // set Tv Serie infos 
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

            // get Serie Tv details 
            $curl = curl_init("https://api.themoviedb.org/3/tv/".$id."?api_key=".$setting->getThemoviedbkey()."&language=".$setting->getThemoviedblang());
            curl_setopt($curl, CURLOPT_FAILONERROR, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
            


            $result = curl_exec($curl);
            $detail_poster =  json_decode($result);
            $title = $detail_poster->name;
            $poster_path = $detail_poster->poster_path;
            $cover_path = $detail_poster->backdrop_path;
            $description = $detail_poster->overview;

            $duration = $detail_poster->number_of_seasons . " Seasons";
            $imdb = $detail_poster->vote_average;
            $genres = $detail_poster->genres;
            $label = $detail_poster->status;
            $year = substr($detail_poster->first_air_date, 0, 4);

           

            $serie= new Poster();
            $serie->setTitle($title);
            $serie->setDuration($duration);
            $serie->setPlayas(1);
            $serie->setDownloadas(1);
            $serie->setType("serie");
            if ( $label == "Ended") {
               $serie->setLabel("Ended");
            }
            $serie->setTags("");
            $serie->setRating("0");
            $serie->setImdb($imdb);
            $serie->setDescription($description);
            $serie->setDownloads(0);
            $serie->setShares(0);
            $serie->setViews(0);
            $serie->setComment(true);
            $serie->setEnabled(true);
            $serie->setYear($year);

            // get poster Serie Tv image 

            $url =  "https://image.tmdb.org/t/p/original".$poster_path;
            $fileName = md5(uniqid());
            $fileType = $this->get_image_mime_type($url);
            $fileExt = $this->get_image_ext_type($url);
            $fullName = $fileName.".".$fileExt;

            $uploadTo = $this->container->getParameter('files_directory').$fileExt."/".$fullName;

            file_put_contents($uploadTo, file_get_contents($url)); 

            $seriemedia= new Media();
            $seriemedia->setType($fileType);
            $seriemedia->setExtension($fileExt);
            $seriemedia->setUrl($fullName);
            $seriemedia->setTitre($serie->getTitle());
            $em->persist($seriemedia);
            $em->flush();
            $serie->setPoster($seriemedia);
            // get cover Serie Tv image 
            if ($cover_path != null) {
                $url_cover =  "https://image.tmdb.org/t/p/original".$cover_path;
                $fileCoverName = md5(uniqid());
                $fileCoverType = "image/jpg";
                $fileCoverExt = "jpg";
                $fullCoverName = $fileCoverName.".".$fileCoverExt;

                $uploadTo = $this->container->getParameter('files_directory').$fileCoverExt."/".$fullCoverName;

                file_put_contents($uploadTo, file_get_contents($url_cover)); 

                $cover_seriemedia= new Media();
                $cover_seriemedia->setType($fileCoverType);
                $cover_seriemedia->setExtension($fileCoverExt);
                $cover_seriemedia->setUrl($fullCoverName);
                $cover_seriemedia->setTitre($serie->getTitle());
                $em->persist($cover_seriemedia);
                $em->flush();
                $serie->setCover($cover_seriemedia);
            }
            // get/set Genres

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
                     $serie->addGenre($get_geren);
                }else{
                    $last_genre = $em->getRepository('AppBundle:Genre')->findOneBy(array(),array("position"=>"desc"));
                    $new_position = ($last_genre == null)? 0 :$last_genre->getPosition()+1;
                    $newgenre= new Genre();
                    $newgenre->setTitle($genre->name);
                    $newgenre->setPosition($new_position);
                    $em->persist($newgenre);
                    $em->flush();
                    $serie->addGenre($newgenre);
                }
                $exist = false;
            }
           
            $em->persist($serie);
            $em->flush();

           
            $max_season=0;
            $last_season=$em->getRepository('AppBundle:Season')->findOneBy(array("poster"=>$serie),array("position"=>"desc"));
            if ($last_season != null) {
                $max_season = $last_season->getPosition();
            }

             foreach ($detail_poster->seasons as $key => $s) {
                $season = new Season();
                $season->setPosition($max_season);
                $season->setPoster($serie);
                $season->setTitle($s->name);
                $em->persist($season);
                $em->flush();  
                $max_season++;
            }
             $this->addFlash('success', 'Operation has been done successfully');
             return $this->redirect($this->generateUrl('app_serie_import_keywords',array("id"=>$id,"serie"=>$serie->getId())));

        }
       return $this->render("AppBundle:Serie:import.html.twig",array("setting"=>$setting,"form"=>$form->createView()));
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
                ->where("p.enabled = true","p.type like 'serie' ")
                ->addOrderBy('p.'.$order, $dir)
                ->addOrderBy('p.id', 'ASC')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();
            }else{
                 $query = $repository->createQueryBuilder('p')
                ->leftJoin('p.genres', 'g')
                ->where("p.enabled = true","p.type like 'serie' ",'g.id = ' . $genre)
                ->addOrderBy('p.'.$order, $dir)
                ->addOrderBy('p.id', 'ASC')
                ->setFirstResult($nombre * $page)
                ->setMaxResults($nombre)
                ->getQuery();         
            }
        $posters_list = $query->getResult();
        return $this->render('AppBundle:Serie:api_all.html.php', array("posters_list" => $posters_list));
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
            ->where("p.enabled = true","p.type like 'serie' ",'g.id in (' . $genres . ')')
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults($nombre)
            ->getQuery();
        $posters_list = $query->getResult();
        return $this->render('AppBundle:Serie:api_all.html.php', array("posters_list" => $posters_list));
    }


    
    public function indexAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $q = " ";
        if ($request->query->has("q") and $request->query->get("q") != "") {
            $q .= " AND  p.title like '%" . $request->query->get("q") . "%'";
        }

        $dql = "SELECT p FROM AppBundle:Poster p  WHERE p.type like 'serie' " . $q . " ORDER BY p.created desc ";
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $series = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            16
        );
        $series_count = $em->getRepository('AppBundle:Poster')->countSeries();
        return $this->render('AppBundle:Serie:index.html.twig', array("series_count" => $series_count, "series" => $series));
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

        if (($image_type = exif_imagetype($image_path)) && (array_key_exists($image_type ,$mimes)))
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

    public function addAction(Request $request)
    {
        $trailer_select=1;
        $serie= new Poster();
        $form = $this->createForm(SerieType::class,$serie);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                if( $serie->getFileposter()!=null or (isset($_POST["image_url"]) and $_POST["image_url"]!=null and $_POST["image_url"]!="" and strpos($_POST["image_url"], 'http') === 0)){
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
                
                    $serie->setType("serie");
                    $serie->setRating("0");
                    if($serie->getFileposter()!=null ){

                        $media= new Media();
                        $media->setFile($serie->getFileposter());
                        $media->upload($this->container->getParameter('files_directory'));
                        $em->persist($media);
                        $em->flush();
                        $serie->setPoster($media);
                    }else{
                        if (isset($_POST["image_url"]) and $_POST["image_url"]!=null and $_POST["image_url"]!="" and strpos($_POST["image_url"], 'http') === 0) {
                            $url =  $_POST["image_url"];
                            $fileName = md5(uniqid());
                            $fileType = $this->get_image_mime_type($url);
                            $fileExt = $this->get_image_ext_type($url);
                            $fullName = $fileName.".".$fileExt;

                            $uploadTo = $this->container->getParameter('files_directory').$fileExt."/".$fullName;

                            file_put_contents($uploadTo, file_get_contents($url)); 

                            $seriemedia= new Media();
                            $seriemedia->setType($fileType);
                            $seriemedia->setExtension($fileExt);
                            $seriemedia->setUrl($fullName);
                            $seriemedia->setTitre($serie->getTitle());
                            $em->persist($seriemedia);
                            $em->flush();
                            $serie->setPoster($seriemedia);
                        }
                    }
                    if($serie->getFilecover()!=null ){
                        $mediacover= new Media();
                        $mediacover->setFile($serie->getFilecover());
                        $mediacover->upload($this->container->getParameter('files_directory'));
                        $em->persist($mediacover);
                        $em->flush();
                        $serie->setCover($mediacover);
                    }

                        if(strlen($serie->getTrailerurl())>1 ){
                            $trailer = new  Source();
                            $trailer->setType("youtube");
                            $trailer->setUrl($serie->getTrailerurl());
                            $em->persist($trailer);
                            $em->flush();

                            $serie->setTrailer($trailer);

                        }
                    


                    $em->persist($serie);
                    $em->flush();

                   
                    $this->addFlash('success', 'Operation has been done successfully');
                    return $this->redirect($this->generateUrl('app_serie_index'));
                }else{
                    $error = new FormError("Required image file");
                    $form->get('fileposter')->addError($error);
                }
       }
       return $this->render("AppBundle:Serie:add.html.twig",array("trailer_select"=> $trailer_select,"form"=>$form->createView()));
    }

  public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $serie = $em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"serie"));
        if($serie==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $slide = $em->getRepository("AppBundle:Slide")->findOneBy(array("poster"=>$serie));

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

            foreach ($serie->getSeasons() as  $season) {
                foreach ($season->getEpisodes() as $episode) {
                    foreach ($episode->getSources() as $key => $source) {
                        $media_source = $source->getMedia();

                        $em->remove($source);
                        $em->flush();

                        if ($media_source!=null) {
                            $media_source->delete($this->container->getParameter('files_directory'));
                            $em->remove($media_source);
                            $em->flush();
                        }
                    }
                    foreach ($episode->getSubtitles() as $key => $subtitle) {
                        $media_subtitle = $subtitle->getMedia();
                        
                        $em->remove($subtitle);
                        $em->flush();

                        if ($media_subtitle!=null) {
                            $media_subtitle->delete($this->container->getParameter('files_directory'));
                            $em->remove($media_subtitle);
                            $em->flush();
                        }
                    }
                    $media_episode = $episode->getMedia();

                    $em->remove($episode);
                    $em->flush();

                    if ($media_episode!=null) {
                        $media_episode->delete($this->container->getParameter('files_directory'));
                        $em->remove($media_episode);
                        $em->flush();
                    }
                }
                $em->remove($season);
                $em->flush();
            }

            $media_cover = $serie->getCover();
            $media_poster = $serie->getPoster();

            $em->remove($serie);
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

            $trailer = $serie->getTrailer();

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
           return $this->redirect($this->generateUrl('app_serie_index'));
        }
        return $this->render('AppBundle:Serie:delete.html.twig',array("form"=>$form->createView()));
    }

    public function trailerAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $serie=$em->getRepository("AppBundle:Poster")->find($id);
        if ($serie==null) {
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
                    $serie->setTrailer($source);
                    $em->flush();
                }
            }else{
                if(strlen($source->getUrl())>1 ){
                    $source->setType($choices[$source->getType()]);
                    $em->persist($source);
                    $em->flush();
                    $serie->setTrailer($source);
                    $em->flush();
                }
            }
        }
        return $this->render("AppBundle:Serie:trailer.html.twig",array("trailer_form"=>$trailer_form->createView(),"serie"=>$serie));
    }
    public function castAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $serie=$em->getRepository("AppBundle:Poster")->find($id);
        if ($serie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $role = new Role();
        $role_form = $this->createForm(RoleType::class,$role);
        $role_form->handleRequest($request);
        if ($role_form->isSubmitted() && $role_form->isValid()) {
                $max=0;
                $roles=$em->getRepository('AppBundle:Role')->findBy(array("poster"=>$serie));
                foreach ($roles as $key => $value) {
                    if ($value->getPosition()>$max) {
                        $max=$value->getPosition();
                    }
                }
                $role->setPosition($max+1);
                $role->setPoster($serie);
                $em->persist($role);
                $em->flush();  
        }
        return $this->render("AppBundle:Serie:cast.html.twig",array("role_form"=>$role_form->createView(),"serie"=>$serie));
    }
    public function seasonsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $serie=$em->getRepository("AppBundle:Poster")->find($id);
        if ($serie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $season = new Season();
        $season_form = $this->createForm(SeasonType::class,$season);
        $season_form->handleRequest($request);
        if ($season_form->isSubmitted() && $season_form->isValid()) {
                $max=0;
                $seasons=$em->getRepository('AppBundle:Season')->findBy(array("poster"=>$serie));
                foreach ($seasons as $key => $value) {
                    if ($value->getPosition()>$max) {
                        $max=$value->getPosition();
                    }
                }
                $season->setPosition($max+1);
                $season->setPoster($serie);
                $em->persist($season);
                $em->flush();  
                $season = new Season();
                $season_form = $this->createForm(SeasonType::class,$season);

        }
        return $this->render("AppBundle:Serie:seasons.html.twig",array("season_form"=>$season_form->createView(),"serie"=>$serie));
    }

    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $serie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"serie"));
        if ($serie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(EditSerieType::class,$serie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $serie->getFilecover()!=null ){
                $media_cover= new Media();
                $media_cover_old=$serie->getCover();
                $media_cover->setFile($serie->getFilecover());
                $media_cover->upload($this->container->getParameter('files_directory'));
                $em->persist($media_cover);
                $em->flush();

                $serie->setCover($media_cover);
                if ($media_cover_old!=null) {
                    $media_cover_old->delete($this->container->getParameter('files_directory'));
                    $em->remove($media_cover_old);
                    $em->flush();
                }
            }
            if( $serie->getFileposter()!=null ){
                $media_poster= new Media();
                $media_poster_old=$serie->getPoster();
                $media_poster->setFile($serie->getFileposter());
                $media_poster->upload($this->container->getParameter('files_directory'));
                $em->persist($media_poster);
                $em->flush();
                
                $serie->setPoster($media_poster);
                $media_poster_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_poster_old);
                $em->flush();
            }
            $em->persist($serie);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_serie_index'));
        }
        return $this->render("AppBundle:Serie:edit.html.twig",array("serie"=>$serie,"form"=>$form->createView()));
    }
    public function commentsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $serie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"serie"));
        if ($serie==null) {
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
       $count=$em->getRepository('AppBundle:Comment')->countByPoster($serie->getId());
        
        return $this->render('AppBundle:Serie:comments.html.twig',
            array(
                'pagination' => $pagination,
                'serie' => $serie,
                'count' => $count,
            )
        );
    }
    public function ratingsAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $serie=$em->getRepository("AppBundle:Poster")->findOneBy(array("id"=>$id,"type"=>"serie"));
        if ($serie==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $rates_1 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$serie,"value"=>1));
        $rates_2 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$serie,"value"=>2));
        $rates_3 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$serie,"value"=>3));
        $rates_4 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$serie,"value"=>4));
        $rates_5 = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$serie,"value"=>5));
        $rates = $em->getRepository('AppBundle:Rate')->findBy(array("poster"=>$serie));


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
        $count=$em->getRepository('AppBundle:Rate')->countByPoster($serie->getId());
        
        $em= $this->getDoctrine()->getManager();
        $dql        = "SELECT c FROM AppBundle:Rate c  WHERE c.poster = ". $id ." ORDER BY c.created desc ";
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
            10
        );
        return $this->render("AppBundle:Serie:ratings.html.twig", array("pagination"=>$pagination,"count"=>$count,"rating"=>$rating,"ratings"=>$ratings,"values"=>$values,"serie" => $serie));

    }


}
?>