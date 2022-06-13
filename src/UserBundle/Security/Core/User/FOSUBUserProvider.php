<?php
namespace UserBundle\Security\Core\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use MediaBundle\Entity\Media;

class FOSUBUserProvider extends BaseClass
{
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array("username" => $username,"type"=>$service))) {
            $previousUser->setUsername(null);
            $previousUser->setPassword(null);
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->setUsername($username);
        $user->setPassword($response->getAccessToken());
        $this->userManager->updateUser($user);
    }
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {



        $username = $response->getUsername();
        $name = $response->getRealName();
        $service = $response->getResourceOwner()->getName();

        $picture = $response->getProfilePicture();

        $media = new Media();
        $media->setTitre($name);
        $media->setUrl($picture);
        $media->setExtension("png");
        $media->setType("url");

        $user = $this->userManager->findUserBy(array("username" => $username,"type"=>$service));
        //when the user is registrating
        if (null === $user) {
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            // create new user here
            $user = $this->userManager->createUser();
            $user->setUsername($username);
            $user->setPassword($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($username);
            $user->setEmail($username);
            $user->setName($name);
            $user->setType($service);
            $user->setMedia($media);

            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            return $user;
        }
        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        //update access token
        $user->setPassword($response->getAccessToken());
        return $user;
    }
}
