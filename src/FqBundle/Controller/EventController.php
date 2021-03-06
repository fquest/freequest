<?php

namespace FqBundle\Controller;

use FqBundle\Entity\Category;
use FqBundle\Entity\Event;
use FqBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FqBundle\Entity\Route as LocationRoute;
use FqBundle\Entity\Location;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use FqBundle\View\Form\Event as EventForm;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\HttpFoundation\Cookie;

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
        return $this->render(
            'FqBundle:Event:create.html.twig',
            [
                'form' => $this->createForm(new EventForm())->createView(),
                'form_action' => $this->generateUrl('event_save'),
                'form_title' => 'Создать событие'
            ]
        );
    }

    /**
     * @Route("/save", name="event_save")
     */
    public function saveAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm(new EventForm(), $event);
        $form->handleRequest($request);
        $date = $form->getData()->getSchedule();
        if ($form->isValid() && $this->validateDate($date)) {
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
        $message = ['text' => 'Неверные данные для создания события!', 'type' => 'danger'];
        $this->get('session')->set('messages', [$message]);
        return $this->redirect($this->generateUrl('event_create'));
    }

    /**
     * @Route("/update/{id}", name="event_update")
     */
    public function updateAction($id, Request $request)
    {
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if ($this->getUser()->getId() != $event->getCreator()->getId()) {
            throw new InsufficientAuthenticationException();
        }
        $form = $this->createForm(new EventForm(), $event);
        $form->handleRequest($request);
        $date = $form->getData()->getSchedule();
        if ($form->isValid() && $this->validateDate($date)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirect($this->generateUrl('event_view', ['id' => $event->getId()]));
        }
        $message = ['text' => 'Неверные данные для создания события!', 'type' => 'danger'];
        $this->get('session')->set('messages', [$message]);
        return $this->redirect($this->generateUrl('event_create'));
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    protected function validateDate(\DateTime $date)
    {
        $currentDate = new \DateTime();
        $dateConstraint = new GreaterThanOrEqual($currentDate);
        $errorList = $this->get('validator')->validateValue($date, $dateConstraint);
        return (count($errorList) == 0);
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

        /** @var \FqBundle\Entity\Event $event */
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);

        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id ' . $id
            );
        }

        if (!isset($_COOKIE['visitedEvent'])) {
        setcookie("visitedEvent[{$event->getId()}]", $event->getId());
        $event->setViews($event->getViews() + 1);
        }
        elseif (!in_array($event->getId(), $_COOKIE['visitedEvent'])) {
            setcookie("visitedEvent[{$event->getId()}]", $event->getId());
            $event->setViews($event->getViews() + 1);
        }
        $this->getDoctrine()->getManager()->flush();

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
            $hiddenEvents = $user->getHiddenEvents();
        } else {
            $user = new User();
            $user->setUsername('Гость');
        }

        $selectedCategories = $request->query->get('categories');
        $searchQuery = $request->get('query');

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->getDoctrine()->getRepository('FqBundle:Event');
        $builder = $repository->createQueryBuilder('n');

        $parameters = [];
        if ($searchQuery) {
            $queryParts = explode(' ', $searchQuery);
            foreach ($queryParts as $key => $part) {
                if (!$key) {
                    $builder->where('n.title LIKE :part' . $key)
                        ->orWhere('n.description LIKE :part' . $key);
                } else {
                    $builder->orWhere('n.title LIKE :part' . $key)
                        ->orWhere('n.description LIKE :part' . $key);
                }

                $parameters['part' . $key] = '%' . $part . '%';
            }
        }

        if ($selectedCategories) {
            $builder->andWhere($builder->expr()->in('n.category', $selectedCategories));
        }

        $builder->setParameters($parameters);

        $events = $builder->getQuery()->getResult();

        if (!empty($hiddenEvents) && count($hiddenEvents->getKeys())) {
            $resultEvents = [];
            foreach ($events as $event) {
                if (!in_array($event, $hiddenEvents->getValues())) {
                    $resultEvents[] = $event;
                }
            }
            $events = $resultEvents;
        }

        $categories = $this->getDoctrine()
            ->getRepository('FqBundle:Category')
            ->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $events,
            $request->query->get('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render(
            'FqBundle:Event:list.html.twig',
            [
                'events' => $events,
                'categories' => $categories,
                'selectedCategories' => $selectedCategories,
                'query' => $searchQuery,
                'user' => $user,
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="event_edit")
     */
    public function editAction($id)
    {
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if ($this->getUser()->getId() != $event->getCreator()->getId()) {
            throw new InsufficientAuthenticationException();
        }
        return $this->render(
            'FqBundle:Event:create.html.twig',
            [
                'form' => $this->createForm(new EventForm(), $event)->createView(),
                'form_title' => sprintf('Редактировать событие "%s"', $event->getTitle()),
                'form_action' => $this->generateUrl('event_update', ['id' => $event->getId()])
            ]
        );
    }

    /**
     * @Route("/delete/{id}", name="event_delete")
     */
    public function deleteAction($id)
    {
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if ($this->getUser()->getId() != $event->getCreator()->getId()) {
            throw new InsufficientAuthenticationException();
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($event);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('event_list'));
    }
}
