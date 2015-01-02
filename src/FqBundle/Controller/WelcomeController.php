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
}
