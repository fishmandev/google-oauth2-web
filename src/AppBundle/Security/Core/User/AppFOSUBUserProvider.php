<?php

namespace AppBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;

/**
 * Class AppFOSUBUserProvider
 * @package AppBundle\Security\Core\User
 */
class AppFOSUBUserProvider extends FOSUBUserProvider
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $userEmail = $response->getEmail();
        $user = $this->userManager->findUserByEmail($userEmail);

        if (null === $user) {
            $user = $this->userManager->createUser();
            $user->setUsername($response->getRealName());
            $user->setEmail($userEmail);
            $user->setPassword('');
            $user->setEnabled(true);
            $this->setAccessToken($user, $response);
            $this->userManager->updateUser($user);
        } else {
            $this->setAccessToken($user, $response);
        }

        return $user;
    }

    /**
     * @param $user
     * @param UserResponseInterface $response
     */
    protected function setAccessToken($user, UserResponseInterface $response)
    {
        $resourceName = $response->getResourceOwner()->getName();
        $user->{'set' . ucfirst($resourceName) . 'AccessToken'}($response->getAccessToken());
    }
}
