<?php

namespace FqBundle\Controller;

use FqBundle\Entity\Category;
use FqBundle\Entity\Event;
use FqBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/event")
 */
class EventController extends Controller
{
    /**
     * @Route("/create", name="event_create")
     */
    public function createAction()
    {
        $event = new Event();
        $event->setTitle('default title')
            ->setDescription('default description');
        $form = $this->createFormBuilder($event)
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('image', 'vlabs_file')
            ->add('category', 'entity', ['class' => 'FqBundle\Entity\Category'])
            ->add('address', 'text')
            ->add('position', 'hidden')
            ->add('schedule', 'collot_datetime')
            ->add('save', 'submit', ['label' => 'Create Event'])
            ->getForm();
        $formView = $form->createView();
        return $this->render('FqBundle:Event:create.html.twig', ['form' => $formView]);
    }

    /**
     * @Route("/save", name="event_save")
     */
    public function saveAction(Request $request)
    {
        $event = new Event();
        $form = $this->createFormBuilder($event)
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('image', 'vlabs_file')
            ->add('category', 'entity', ['class' => 'FqBundle\Entity\Category'])
            ->add('address', 'text')
            ->add('position', 'hidden')
            ->add('schedule', 'collot_datetime')
            ->add('save', 'submit', ['label' => 'Create Event'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            //@todo resolve cascade save (to use just getUser without loding user entity)
            $creator = $this->getDoctrine()
                ->getRepository('FqBundle:User')
                ->find($this->getUser()->getId());
            $event->setCreator($creator);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('event_view', ['id' => $event->getId()]));
        }
        return $this->redirect($this->generateUrl('event_create'));
    }

    /**
     * @Route("/view/{id}", name="event_view")
     */
    public function viewAction($id)
    {
        if ($this->getUser()) {
            /** @var \FqBundle\Entity\User $user */
            $user = $this->getDoctrine()
                ->getRepository('FqBundle:User')
                ->find($this->getUser()->getId());
        } else {
            $user = new User();
            $user->setUsername('Гость');
        }

        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id ' . $id
            );
        }
        return $this->render(
            'FqBundle:Event:view.html.twig',
            [
                'event' => $event,
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/list", name="event_list")
     */
    public function listAction(Request $request)
    {
        if ($this->getUser()) {
            /** @var \FqBundle\Entity\User $user */
            $user = $this->getDoctrine()
                ->getRepository('FqBundle:User')
                ->find($this->getUser()->getId());
        } else {
            $user = new User();
            $user->setUsername('Гость');
        }

        $selectedCategories = $request->query->get('categories');
        if ($selectedCategories) {
            $qb = $this->getDoctrine()->getRepository('FqBundle:Event')->createQueryBuilder('n');
            $events = $qb->where($qb->expr()->in('n.category', $selectedCategories))->getQuery()->getResult();
        }  else {
            $events = $this->getDoctrine()
                ->getRepository('FqBundle:Event')
                ->findAll();
        }
        $categories = $this->getDoctrine()
            ->getRepository('FqBundle:Category')
            ->findAll();
        return $this->render(
            'FqBundle:Event:list.html.twig',
            [
                'events' => $events,
                'categories' => $categories,
                'selectedCategories' => $selectedCategories,
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/category/save", name="save_category")
     */
    public function saveCategoryAction(Request $request)
    {
        $name = $request->get('name');
        if ($name) {
            $category = new Category();
            $category->setName($name);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return new Response(json_encode(['name' => $name, 'id' => $category->getId()]));
        }
        return new Response(json_encode(['error' => 'error']));
    }

}
