<?php
/**
 * Created by PhpStorm.
 * User: sivashchenko
 * Date: 12/9/2014
 * Time: 6:33 PM
 */

namespace FqBundle\Entity;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Acl\Exception\Exception;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->entityManager->getRepository('FqBundle:User')->findBy(['username' => $username]);
        if (!$user) {
            $user = new User();
            $user->setUsername($username);
        }
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $fbid = $response->getUsername();
        $user = $this->entityManager->getRepository('FqBundle:User')->findOneBy(['fbid' => $fbid]);
        if (!$user) {
            $user = new User();
            $user->setUsername($response->getRealName())
                ->setFbid($fbid)
                ->setEmail($response->getEmail())
                ->setPicture($response->getProfilePicture());
            $this->entityManager->persist($user);
        }
        $user->setUsername($response->getRealName())
            ->setFbid($fbid)
            ->setEmail($response->getEmail())
            ->setPicture($response->getProfilePicture());
        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }
        if (!$user->getId()) {
            throw new Exception('User does not have id!');
        }
        //@todo why do we need this method. take a look on usages
//        $this->entityManager->persist($user);
//        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'FqBundle\Entity\User';
    }
} 