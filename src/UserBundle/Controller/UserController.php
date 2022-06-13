<?php 
namespace UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Form\Model\ChangePassword;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use UserBundle\Form\UserType;
use UserBundle\Form\ProfileType;
use MediaBundle\Entity\Media as Media;
use AppBundle\Entity\Transaction;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
class UserController extends Controller
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

    public function commentAction(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if ($user==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($user->hasRole("ROLE_ADMIN")) {
            throw new NotFoundHttpException("Page not found");
        }

        $dql        = "SELECT c FROM AppBundle:Comment c  WHERE c.user = ".$user->getId();
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render(
                'UserBundle:User:comment.html.twig',array(
                    'pagination' => $pagination,
                    'user'=>$user
                )
                );
    }
    public function myeditAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager();
        $id = $this->getUser()->getId();

        $user = $em->getRepository("UserBundle:User")->find($id);

        
        $form = $this->createForm(ProfileType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if( $user->getFile()!=null ){
                $media= new Media();
                $media_old=$user->getMedia();
                $media->setFile($user->getFile());
                $media->upload($this->container->getParameter('files_directory'));
                $em->persist($media);
                $em->flush();
                $user->setMedia($media);
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $em->flush();
        }
        return $this->render(
                'UserBundle:Profile:edit.html.twig',array(
                    "form"=>$form->createView()
                )
        );
    }
  
    public function editAction(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if ($user==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($user->hasRole("ROLE_ADMIN")) {
            throw new NotFoundHttpException("Page not found");
        }

        
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success','Operation has been done successfully');
           return $this->redirect($this->generateUrl('user_user_index'));
        }
        return $this->render(
                'UserBundle:User:edit.html.twig',array(
                    "form"=>$form->createView(),
                    'user'=>$user
                )
        );
    }
  
    public function deleteAction($id,Request $request){
        $em=$this->getDoctrine()->getManager();

        $user = $em->getRepository("UserBundle:User")->find($id);
        if($user==null){
            throw new NotFoundHttpException("Page not found");
        }

        $form=$this->createFormBuilder(array('id' => $id))
            ->add('id', HiddenType::class)
            ->add('Yes', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $media_old = $user->getMedia();
            $em->remove($user);
            $em->flush();
            if( $media_old!=null ){
                $media_old->delete($this->container->getParameter('files_directory'));
                $em->remove($media_old);
                $em->flush();
            }
            $this->addFlash('success', 'Operation has been done successfully');
            return $this->redirect($this->generateUrl('user_user_index'));
        }
        return $this->render('UserBundle:User:delete.html.twig',array("form"=>$form->createView()));
    }

    function number_format_short( $n ) {
    $precision = 1;
      if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
      // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
      // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ( $precision > 0 ) {
            $dotzero = '.' . str_repeat( '0', $precision );
            $n_format = str_replace( $dotzero, '', $n_format );
        }
        return $n_format . $suffix;
    }

    public function ratingsAction(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if ($user==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($user->hasRole("ROLE_ADMIN")) {
            throw new NotFoundHttpException("Page not found");
        }

        $dql        = "SELECT c FROM AppBundle:Rate c  WHERE c.user = ".$user->getId();
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render(
                'UserBundle:User:ratings.html.twig',array(
                    'user'=>$user,
                    "pagination"=>$pagination,
                )
                );
    }

    public function indexAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager();
        $users = $em->getRepository("UserBundle:User")->findAll();

        $q=" AND ( 1=1 ) ";
        if ($request->query->has("q") and $request->query->get("q")!="") {
           $q.=" AND ( u.name like '%".$request->query->get("q")."%' or u.username like '%".$request->query->get("q")."%') ";
        }
        $dql        = "SELECT u FROM UserBundle:User u  WHERE (NOT u.roles LIKE '%ROLE_ADMIN%')   " .$q ." ";
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render("UserBundle:User:index.html.twig",array(
            'pagination' => $pagination,
            "users"=>$users
        ));
    }
 
    public function api_editAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $name=str_replace('"',"",$request->get("name"));
        $id=$request->get("id");
        $key=str_replace('"',"",$request->get("key"));
        
        $code="200";
        $message="";
        $errors=array();

        $em = $this->getDoctrine()->getManager();

        $user=$em->getRepository('UserBundle:User')->find($id);

        if (!$user) {
            throw new NotFoundHttpException("Page not found");  
        }
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        if (sha1($user->getPassword()) == $key) {

                if($request->files->get('uploaded_file')){
                    $old_media=$user->getMedia();
                    $media= new Media();
                    $media->setFile($request->files->get('uploaded_file'));
                    $media->upload($this->container->getParameter('files_directory'));
                    $media->setEnabled(true);
                    $em->persist($media);
                    $em->flush();
                    $user->setMedia($media);
                    if($old_media!=null){
                            $old_media->delete($this->container->getParameter('files_directory'));
                            $em->remove($old_media);
                            $em->flush();
                    }
                    $em->flush();

                    if($user->getMedia() ==  null ){
                        $errors[]=array("name"=>"url","value"=>"https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg");   
                    }else{
                        if ($user->getMedia()->getType()=="link") {
                            $errors[]=array("name"=>"url","value"=>$user->getMedia()->getUrl());   
                        }else{
                            $errors[]=array("name"=>"url","value"=>$imagineCacheManager->getBrowserPath($user->getMedia()->getLink(), 'actor_thumb')) ;   
                        }
                    }
                }
                $user->setName($name);

                $em->flush();
                $errors[]=array("name"=>"name","value"=>$user->getName());   

                $code=200;  
                $message="Your infos has been successfully edit";
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
     public function api_tokenAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $token_f=$request->get("token_f");;
        $user=$request->get("user");
        $key=$request->get("key");
        $name=$request->get("name");

        $code="200";
        $message="";
        $errors=array();

        $em = $this->getDoctrine()->getManager();

        $user=$em->getRepository('UserBundle:User')->find($user);

        if (!$user) {
            throw new NotFoundHttpException("Page not found");  
        }
        if (sha1($user->getPassword()) == $key) {
               // $user->setToken($token_f);
                $user->setName($name);

                $em->flush();
                $code=200;  
                $message="You have successfully connected";
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

    public function api_registerAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $username=$request->get("username");
        $password=$request->get("password");
        $name=$request->get("name");
        $type=$request->get("type");
        $image=$request->get("image");
        $imagineCacheManager = $this->get('liip_imagine.cache.manager');


        $code="200";
        $message="";
        $errors=array();
        $em = $this->getDoctrine()->getManager();
        $u=$em->getRepository('UserBundle:User')->findOneByUsername($username);
        if ($u!=null) {
                if ($u->getType()=="email") {
                    $code=500;
                    $message="this email address already exists";
                    $errors[]=array("name"=>"username","value"=>"this email address already exists");
                }else{
                    $code=200;
                    $message="You have successfully logged in";
                    $errors[]=array("name"=>"id","value"=>$u->getId());
                    $errors[]=array("name"=>"name","value"=>$u->getName());
                    $errors[]=array("name"=>"username","value"=>$u->getUsername());
                    $errors[]=array("name"=>"salt","value"=>$u->getSalt());
                    $errors[]=array("name"=>"type","value"=>$u->getType());
                    $errors[]=array("name"=>"token","value"=>sha1($u->getPassword()));
                    $errors[]=array("name"=>"subscribed","value"=>($u->isSubscribed())?"TRUE":"FALSE");

                    if($u->getMedia() ==  null ){
                        $errors[]=array("name"=>"url","value"=>"https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg");   
                    }else{
                        if ($u->getMedia()->getType()=="link") {
                            $errors[]=array("name"=>"url","value"=>$u->getMedia()->getUrl());   
                        }else{
                            $errors[]=array("name"=>"url","value"=>$imagineCacheManager->getBrowserPath($u->getMedia()->getLink(), 'actor_thumb')) ;   
                        }
                    }
                    $errors[]=array("name"=>"enabled","value"=>$u->isEnabled());        
                }
        }else{
            $user = new User();
            if (sizeof($errors)==0) {

                $media= new Media();
                $media->setFile($request->files->get('uploaded_file'));
                $media->setUrl($image);
                $media->setType("link");
                $media->setExtension("jpeg");
                
                $media->setEnabled(true);
                $em->persist($media);
                $em->flush();


                $user->setUsername($username);
                $user->setPlainPassword($password);
                $user->setEmail($username);
                $user->setEnabled(true);
                $user->setName($name);
                $user->setType($type);
                $user->setMedia($media);
                $em->persist($user);
                $em->flush();
                $code=200;
                $message="You have successfully registered";
                $errors[]=array("name"=>"id","value"=>$user->getId());
                $errors[]=array("name"=>"name","value"=>$user->getName());
                $errors[]=array("name"=>"username","value"=>$user->getUsername());
                $errors[]=array("name"=>"salt","value"=>$user->getSalt());
                $errors[]=array("name"=>"subscribed","value"=>($user->isSubscribed())?"TRUE":"FALSE");

                if($user->getMedia() ==  null ){
                    $errors[]=array("name"=>"url","value"=>"https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg");   
                }else{
                    if ($user->getMedia()->getType()=="link") {
                        $errors[]=array("name"=>"url","value"=>$user->getMedia()->getUrl());   
                    }else{
                        $errors[]=array("name"=>"url","value"=>$imagineCacheManager->getBrowserPath($user->getMedia()->getLink(), 'actor_thumb')) ;   
                    }
                }

                $errors[]=array("name"=>"type","value"=>$user->getType());
                $errors[]=array("name"=>"token","value"=>sha1($user->getPassword()));
                $errors[]=array("name"=>"enabled","value"=>$user->isEnabled());  
                $errors[]=array("name"=>"registered","value"=>"true");  
            }else{
                $code=500;
                $message="validation error";
            }
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
  public function api_loginAction(Request $request,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        $username=$request->get("username");
        $password=$request->get("password");

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');


        $code="200";
        $message="";
        $errors=array();
        $em = $this->getDoctrine()->getManager();
        $u=$em->getRepository('UserBundle:User')->findOneByUsername($username);
        if ($u!=null) {
                if ($u->getType()=="email") {
                    $code=200;
                    $message="You have successfully logged in";
                    $errors[]=array("name"=>"id","value"=>$u->getId());
                    $errors[]=array("name"=>"name","value"=>$u->getName());
                    $errors[]=array("name"=>"username","value"=>$u->getUsername());
                    $errors[]=array("name"=>"salt","value"=>$u->getSalt());
                    $errors[]=array("name"=>"type","value"=>$u->getType());
                    $errors[]=array("name"=>"token","value"=>sha1($u->getPassword()));
                    $errors[]=array("name"=>"subscribed","value"=>($u->isSubscribed())?"TRUE":"FALSE");
                    if($u->getMedia() ==  null ){
                        $errors[]=array("name"=>"url","value"=>"https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg");   
                    }else{
                        if ($u->getMedia()->getType()=="link") {
                            $errors[]=array("name"=>"url","value"=>$u->getMedia()->getUrl());   
                        }else{
                            $errors[]=array("name"=>"url","value"=>$imagineCacheManager->getBrowserPath($u->getMedia()->getLink(), 'actor_thumb')) ;   
                        }
                    }
                    $errors[]=array("name"=>"enabled","value"=>$u->isEnabled());        
                }else{
                    $code=500;
                    $message="Invalide username / password";
                }
        }else{
             $code=500;
            $message="Invalide username / password";
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

    public function api_checkAction($id,$key,$token)
    {
        $code="500";
        $message="";
        $errors=array();
        if ($token!=$this->container->getParameter('token_app')) {
            $code=500;
        }

        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository('UserBundle:User')->findOneBy(array("id"=>$id));

        if($user){
            if ($user->isEnabled()) {
                if ($key==sha1($user->getPassword())) {
                    $code=200;
                }else{
                    $code=500;
                }
            }else{
                $code=500;
            }
            if ($user->hasRole("ROLE_ADMIN")) {
                $code=500;
            }
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
    public function admin_subscriptionsAction(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if ($user==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($user->hasRole("ROLE_ADMIN")) {
            throw new NotFoundHttpException("Page not found");
        }

        $dql        = "SELECT s FROM AppBundle:Subscription s  WHERE s.user = ".$user->getId();
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render(
                'UserBundle:User:subscriptions.html.twig',array(
                    'user'=>$user,
                    "pagination"=>$pagination,
                )
            );
    }
    public function subscriptionsAction(Request $request)
    {
        $em= $this->getDoctrine()->getManager();
        if ($this->getUser() ==null) {
            throw new NotFoundHttpException("Page not found");
        }
        $user = $this->getUser();

        $dql        = "SELECT c FROM AppBundle:Subscription c  WHERE c.user = ".$user->getId();
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            7
        );

        return $this->render(
                'UserBundle:Profile:subscriptions.html.twig',array(
                    'pagination' => $pagination
                )
        );
    }
    public function commentsAction(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:User")->find($id);
        if ($user==null) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($user->hasRole("ROLE_ADMIN")) {
            throw new NotFoundHttpException("Page not found");
        }

        $dql        = "SELECT c FROM AppBundle:Comment c  WHERE c.user = ".$user->getId();
        $query      = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render(
                'UserBundle:User:comments.html.twig',array(
                    'user'=>$user,
                    "pagination"=>$pagination,
                )
                );
    }
    public function api_change_passwordAction($id,$password,$new_password,$token)
    {
        if ($token!=$this->container->getParameter('token_app')) {
            throw new NotFoundHttpException("Page not found");  
        }
        
        $code="200";
        $message="";
        $errors=array();
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository('UserBundle:User')->findOneBy(array("id"=>$id));  
        if ($user->hasRole("ROLE_ADMIN")) {
            throw new NotFoundHttpException("Page not found");
        }
        if ($user->getType()!="email") {
            throw new NotFoundHttpException("Page not found");
        }
        if ($user) {
            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);
            if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
                if (strlen($new_password)<6) {
                    $code=500;
                    $errors["password"]="This value very short !";
                }else{
                    $newPasswordEncoded = $encoder->encodePassword($new_password, $user->getSalt());
                    $user->setPassword($newPasswordEncoded);
                    $em->persist($user);
                    $em->flush();
                    $code=200;
                    $message="Password has been changed successfully";
                    $errors[]=array("name"=>"salt","value"=>$user->getSalt());
                    $errors[]=array("name"=>"token","value"=>sha1($user->getPassword()));
                }
            } else {
                $code=500;  
                $message="Current password is incorrect";
            }
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
