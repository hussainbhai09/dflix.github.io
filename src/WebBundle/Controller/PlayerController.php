<?php

namespace WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class PlayerController extends Controller
{


    public function movieAction($id,$slug,$source)
    {


        $premium = false; 
        if ($this->getUser()!=null) {
            $premium = $this->getUser()->isSubscribed();
        } 
        $em = $this->getDoctrine()->getManager();
      	$poster = $em->getRepository('AppBundle:Poster')->findOneBy(array("id"=>$id,"slug"=>$slug));
      	$source = $em->getRepository('AppBundle:Source')->findOneBy(array("id"=>$source,"poster"=>$poster));
        



        if ($source->getPremium() == 2) {
            if ($premium == false) {
                   return $this->redirect($this->generateUrl('wep_subscription_subscribe'));
            }
        }
        $session = new Session();

        if (!$session->has('movie_'.$poster->getId())) {
            $session->set('movie_'.$poster->getId(), 'viewed');
            $poster->setViews($poster->getViews()+1);
            $em->flush();
        }

        return $this->render('WebBundle:Player:movie.html.twig',array(
            "source" => $source,
            "poster" => $poster,
            "premium"=> $premium
        ));
    }
    public function channelAction($id,$slug,$source)
    {

       
        $premium = false;
        if ($this->getUser()!=null) {
            $premium = $this->getUser()->isSubscribed();
        } 
        $em = $this->getDoctrine()->getManager();
        $channel = $em->getRepository('AppBundle:Channel')->findOneBy(array("id"=>$id,"slug"=>$slug));
        $source = $em->getRepository('AppBundle:Source')->findOneBy(array("id"=>$source,"channel"=>$channel));
        if ($source->getPremium() == 2) {
            if ($premium == false) {
                   return $this->redirect($this->generateUrl('wep_subscription_subscribe'));
            }
        }
        $session = new Session();

        if (!$session->has('channel_'.$channel->getId())) {
            $session->set('channel_'.$channel->getId(), 'viewed');
            $channel->setViews($channel->getViews()+1);
            $em->flush();
        }
        return $this->render('WebBundle:Player:channel.html.twig',array(
            "source" => $source,
            "channel" => $channel,
            "premium"=>$premium
        ));
    }
    public function preepisodeAction($id,$slug){
        $em = $this->getDoctrine()->getManager();
        $episode = $em->getRepository('AppBundle:Episode')->findOneBy(array("id"=>$id,"slug"=>$slug));
        $premium = false;
        if ($this->getUser()!=null) {
            $premium = $this->getUser()->isSubscribed();
        } 
        $repository_soure = $em->getRepository('AppBundle:Source');
        $repo_query_source = $repository_soure->createQueryBuilder('c');
        $repo_query_source->leftJoin('c.episode', 'e');
        $repo_query_source->where("e.id = ". $id ,"c.kind like 'play' or c.kind like 'both' " );
   
        $query_source_test =  $repo_query_source->getQuery(); 
        $sources_test = $query_source_test->getResult();
        if (sizeof($sources_test) == 0) {
            $this->addFlash('success', 'They are no sources in this episode ! ');
            return $this->redirect($this->generateUrl('wep_serie_view',array("id"=>$episode->getSeason()->getPoster()->getId(),"slug"=>$episode->getSeason()->getPoster()->getSlug())));
        }

        if ($this->getUser()!=null) {
           if (!$premium) {
                $repo_query_source->andWhere("c.premium like '1' or c.premium like '3'");
           }
        }else{
           $repo_query_source->andWhere("c.premium like '1' or c.premium like '3'");
        }

        $query_source =  $repo_query_source->getQuery(); 
        $sources = $query_source->getResult();
        if (sizeof($sources)>0) {
            return $this->redirect($this->generateUrl('wep_player_episode',array("id"=>$episode->getId(),"source"=>$sources[0]->getId(),"slug"=>$episode->getSlug())));
        }else{
             return $this->redirect($this->generateUrl('wep_subscription_subscribe'));
        }
    }
    public function episodeAction($id,$slug,$source)
    {

        $em = $this->getDoctrine()->getManager();
        $episode = $em->getRepository('AppBundle:Episode')->findOneBy(array("id"=>$id,"slug"=>$slug));
        
        $next_episode = $em->getRepository('AppBundle:Episode')->findOneBy(array("position"=>$episode->getPosition()+1,"season"=>$episode->getSeason()));
        $source = $em->getRepository('AppBundle:Source')->findOneBy(array("id"=>$source,"episode"=>$episode));
        if ($next_episode==null) {
              $next_season = $em->getRepository('AppBundle:Season')->findOneBy(array("position"=>$episode->getSeason()->getPosition()+1,"poster"=>$episode->getSeason()->getPoster()));
              $next_episode = $em->getRepository('AppBundle:Episode')->findOneBy(array("position"=>1,"season"=>$next_season));
        }
  
        $premium = false;
        if ($this->getUser()!=null) {
            $premium = $this->getUser()->isSubscribed();
        } 
        if ($source->getPremium() == 2) {
            if ($premium == false) {
                   return $this->redirect($this->generateUrl('wep_subscription_subscribe'));
            }
        }
        $session = new Session();


        if (!$session->has('episode_'.$episode->getId())) {
            $session->set('episode_'.$episode->getId(), 'viewed');
            $episode->setViews($episode->getViews()+1);
            $em->flush();
            
            $episode->getSeason()->getPoster()->setViews($episode->getSeason()->getPoster()->getViews()+1);
            $em->flush();
        }
        return $this->render('WebBundle:Player:episode.html.twig',array(
            "source" => $source,
            "episode" => $episode,
            "next_episode"=> $next_episode,
            "premium"=>$premium
        ));
    }

}
