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
use Symfony\Component\Security\Core\SecurityContext;
use FqBundle\View\Form\Event as EventForm;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
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
            ['form' => $this->createForm(new EventForm())->createView()]
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
        $currentDate = new \DateTime();
        $dateConstraint = new GreaterThanOrEqual($currentDate);
        $errorList = $this->get('validator')->validateValue($date, $dateConstraint);
        if ($form->isValid() && count($errorList) == 0) {
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

        $event->setViews($event->getViews() + 1);
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

        return $this->render(
            'FqBundle:Event:list.html.twig',
            [
                'events' => $events,
                'categories' => $categories,
                'selectedCategories' => $selectedCategories,
                'query' => $searchQuery,
                'user' => $user
            ]
        );
    }
}
