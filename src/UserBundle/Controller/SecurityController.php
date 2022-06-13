<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use MediaBundle\Entity\Media as Media;
use UserBundle\Entity\User ;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends BaseController {

    public function ajaxloginAction(Request $request){

        if($request->isXmlHttpRequest()) {

        $phone=$_POST["phone"];

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');


        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->findOneByUsername($phone);
        if ($user!=null) {
                  //  return new Response("exist");

        }else{
                $user = new User();

                $media= new Media();
                $media->setUrl("https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg");

                $media->setType("link");
                $media->setExtension("jpeg");
                
                $media->setEnabled(true);
                $em->persist($media);
                $em->flush();


                $user->setUsername($phone);
                $user->setPlainPassword($phone);
                $user->setEmail($phone);
                $user->setEnabled(true);
                $user->setName("NULL");
                $user->setType("phone");
                $user->setSalt("phone");
                $user->setMedia($media);
                $em->persist($user);
                $em->flush();

        }


            $token = new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                'secured_area',
                $user->getRoles()
            );

            $this->get('security.context')->setToken($token);

            $request->getSession()->set('_security_secured_area', serialize($token));
                return new Response("exist");
            }else{
                                return new Response("NOO");

            }

    }
    public function loginAction(Request $request)
    {   

        $securityContext = $this->container->get('security.authorization_checker');
    
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        if (class_exists('\Symfony\Component\Security\Core\Security')) {
            $authErrorKey = Security::AUTHENTICATION_ERROR;
            $lastUsernameKey = Security::LAST_USERNAME;
        } else {
            // BC for SF < 2.6
            $authErrorKey = SecurityContextInterface::AUTHENTICATION_ERROR;
            $lastUsernameKey = SecurityContextInterface::LAST_USERNAME;
        }

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
            "request"=>$request
        ));
    }
    public function renderLogin(array $data) {
        $request = $data["request"];
        if ($request->attributes->get('_route') == 'admin_login') {
            $template = sprintf('FOSUserBundle:Security:login_admin.html.twig');
        } else {
            $template = sprintf('FOSUserBundle:Security:login.html.twig');
        }
        return $this->container->get('templating')->renderResponse($template, $data);
    }
    public function boxAction(Request $request)
    {
                /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        if (class_exists('\Symfony\Component\Security\Core\Security')) {
            $authErrorKey = Security::AUTHENTICATION_ERROR;
            $lastUsernameKey = Security::LAST_USERNAME;
        } else {
            // BC for SF < 2.6
            $authErrorKey = SecurityContextInterface::AUTHENTICATION_ERROR;
            $lastUsernameKey = SecurityContextInterface::LAST_USERNAME;
        }

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        if ($this->has('security.csrf.token_manager')) {
            $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        } else {
            // BC for SF < 2.4
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
        }
         return $this->render('FOSUserBundle:Security:box.html.twig',array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ));
    }

}