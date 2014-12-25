<?php

namespace FqBundle\Controller;

use FqBundle\Entity\Category;
use FqBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            ->add('save', 'submit', ['label' => 'Create Event'])
            ->getForm();
        return $this->render('FqBundle:Event:create.html.twig', ['form' => $form->createView()]);
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
            ['event' => $event]
        );
    }

    /**
     * @Route("/list", name="event_list")
     */
    public function listAction(Request $request)
    {
        /** @var \FqBundle\Entity\User $user */
        $user = $this->getDoctrine()
            ->getRepository('FqBundle:User')
            ->find($this->getUser()->getId());

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
     * @Route("/createcat", name="create_category")
     */
    public function createCategoryAction()
    {
        $event = new Category();
        $event->setName('default category name');
        $form = $this->createFormBuilder($event)
            ->add('name', 'text')
            ->add('save', 'submit', ['label' => 'Create Category'])
            ->getForm();
        return $this->render('FqBundle:Event:category.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/savecat", name="save_category")
     */
    public function saveCategoryAction(Request $request)
    {
        $category = new Category();
        $form = $this->createFormBuilder($category)
            ->add('name', 'text')
            ->add('save', 'submit', ['label' => 'Create Category'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('event_list'));
        }
        return $this->redirect($this->generateUrl('create_category'));
    }

}
