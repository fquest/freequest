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
        $events = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->findAll();
        $categories = $this->getDoctrine()
            ->getRepository('FqBundle:Category')
            ->findAll();
        return $this->render(
            'FqBundle:Welcome:index.html.twig',
            ['events' => $events, 'categories' => $categories]
        );
    }

    /**
     * @Route("/install", name="install")
     */
    public function installAction()
    {
        $categoriesData = [
            ['name' => 'Live Квесты', 'image' => '/bundles/fq/images/quests.png'],
            ['name' => 'Спортивные мероприятия', 'image' => '/bundles/fq/images/sports.png'],
            ['name' => 'Путешествия', 'image' => '/bundles/fq/images/trips.png'],
            ['name' => 'Культурные мероприятия', 'image' => '/bundles/fq/images/party.png'],
            ['name' => 'Настольные игры', 'image' => '/bundles/fq/images/desk_game.png'],
            ['name' => 'Другое', 'image' => '/bundles/fq/images/etc.png'],
        ];

        $entityManager = $this->getDoctrine()->getManager();

        //Remove all categories
        $categories = $this->getDoctrine()
            ->getRepository('FqBundle:Category')
            ->findAll();
        foreach ($categories as $oldCategory) {
            $entityManager->remove($oldCategory);
        }

        // Create new categories
        foreach ($categoriesData as $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setImage($data['image']);
            $entityManager->persist($category);
        }

        $entityManager->flush();
        return $this->redirect($this->generateUrl('homepage'));
    }
}
