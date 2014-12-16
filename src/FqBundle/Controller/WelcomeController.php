<?php

namespace FqBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FqBundle\Entity\Category;
use FqBundle\Entity\Event;
use FqBundle\Entity\User;
use FqBundle\Entity\Image;

class WelcomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        if($this->getUser()) {
            return $this->redirect($this->generateUrl('dashboard'));
        }
        return $this->render('FqBundle:Welcome:index.html.twig');
    }

    /**
     * @Route("/install", name="install")
     */
    public function installAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $userImage = new Image();
        $userImage->setPath('files/sample/user.jpg')
        ->setName('user image');
        $categoryImage = new Image();
        $categoryImage->setPath('files/sample/category.jpg')
        ->setName('category_image');
        $eventImage = new Image();
        $eventImage->setPath('files/sample/event.jpg')
        ->setName('event_image');

        $entityManager->persist($categoryImage);
        $entityManager->persist($eventImage);
        $entityManager->persist($userImage);
        $entityManager->flush();

        $category = new Category();
        $category->setName('Sample Category 1')
            ->setImage($categoryImage);
        $category2 = new Category();
        $category2->setName('Sample Category 2')
            ->setImage($categoryImage);

        $user = new User();
        $user->setUsername('Sample User 1')
            ->setEmail('sample1@sample.com')
            ->setFbid('123')
            ->setPicture($userImage);
        $user2 = new User();
        $user2->setUsername('Sample User 2')
            ->setEmail('sample2@sample.com')
            ->setFbid('456')
            ->setPicture($userImage);

        $event = new Event();
        $event->setCategory($category)
            ->setImage($eventImage)
            ->setImage($eventImage)
            ->setCreator($user)
            ->setTitle('Sample Event 1')
            ->setDescription('Sample Description 1');
        $event2 = new Event();
        $event2->setCategory($category2)
            ->setImage($eventImage)
            ->setCreator($user2)
            ->setTitle('Sample Event 2')
            ->setDescription('Sample Description 2');

        $entityManager->persist($user);
        $entityManager->persist($user2);
        $entityManager->persist($category);
        $entityManager->persist($category2);
        $entityManager->persist($event);
        $entityManager->persist($event2);
        $entityManager->flush();
        return $this->render('FqBundle:Welcome:index.html.twig');
    }
}
