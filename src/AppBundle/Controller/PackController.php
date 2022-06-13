<?php 
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Pack;
use MediaBundle\Entity\Media;
use AppBundle\Form\PackType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class PackController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $packs =   $em->getRepository("AppBundle:Pack")->findBy(array());
        return $this->render("AppBundle:Pack:index.html.twig",array("packs"=>$packs));
    }

    public function api_allAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $em=$this->getDoctrine()->getManager();

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
        $list=array();
        $packs =   $em->getRepository("AppBundle:Pack")->findBy(array());
        foreach ($packs as $key => $pack) {
            $s["id"]=$pack->getId();
            $s["title"]=$pack->getTitle();
            $s["description"]=$pack->getDiscount();
            $s["discount"]=$pack->getDiscount();
            $s["price"]=$pack->getPrice();
            $list[]=$s;
        }
        header('Content-Type: application/json'); 
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent=$serializer->serialize($list, 'json');
        return new Response($jsonContent);
    }

    public function addAction(Request $request)
    {
        $pack= new Pack();
        $form = $this->createForm(PackType::class,$pack);
        $em=$this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pack);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_pack_index'));
       }
        return $this->render("AppBundle:Pack:add.html.twig",array("form"=>$form->createView()));
    }
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $pack = $em->getRepository("AppBundle:Pack")->find($id);
        if($pack==null){
            throw new NotFoundHttpException("Page not found");
        }
        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $packs=$em->getRepository('AppBundle:Pack')->findBy(array());
            $em->remove($pack);
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_pack_index'));
        }
        return $this->render('AppBundle:Pack:delete.html.twig',array("form"=>$form->createView()));
    }
    public function editAction(Request $request,$id)
    {
        $em=$this->getDoctrine()->getManager();
        $pack=$em->getRepository("AppBundle:Pack")->find($id);
        if ($pack==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $form = $this->createForm(PackType::class,$pack);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('app_pack_index'));
 
        }
        return $this->render("AppBundle:Pack:edit.html.twig",array("pack"=>$pack,"form"=>$form->createView()));
    }
}
?>