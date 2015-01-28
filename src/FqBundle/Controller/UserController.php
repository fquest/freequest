<?php

namespace FqBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return [
            'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ];
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        //todo need to redirect user to homepage if the page (http_referer) is only for logged in
//        $redirectUrl = $request->server->get('HTTP_REFERER') ?: $this->generateUrl('homepage');
//        return $this->redirect($redirectUrl);
        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        /** @var \FqBundle\Entity\User $user */
        $user = $this->getDoctrine()
            ->getRepository('FqBundle:User')
            ->find($this->getUser()->getId());
        /** @var \FqBundle\Entity\Event[] $events */
        $events = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->findBy(['creator' => $user->getId()]);
        $passedEvents = [];
        $futureEvents = [];
        $allEvents = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->findBy([] , ['created_at' => 'DESC']);
        $currentDate = new \DateTime();
        foreach ($user->getEvents() as $event) {
            if ($event->getSchedule() > $currentDate) {
                $futureEvents[] = $event;
            } else {
                $passedEvents[] = $event;
            }
        }
        foreach ($events as $event) {
            if ($event->getSchedule() > $currentDate) {
                $futureEvents[] = $event;
            } else {
                $passedEvents[] = $event;
            }
        }
        $nearestEvents = [];
        foreach ($allEvents as $event) {
            $nearestEvents[] = $event;
            if (count($nearestEvents) >= 3) {
                break;
            }
        }
        return $this->render(
            'FqBundle:User:dashboard.html.twig',
            [
                'futureEvents' => $futureEvents,
                'passedEvents' => $passedEvents,
                'nearestEvents' => $nearestEvents,
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/profile/{id}", defaults={"id" = null}, name="profile")
     */
    public function profile($id)
    {
        /** @var \FqBundle\Entity\User $user */
        if ($id) {
            $user = $this->getDoctrine()
                ->getRepository('FqBundle:User')
                ->find($id);
        } else {
            $user = $this->getUser();
        }

        if (!$user) {
            throw $this->createNotFoundException(
                'User does not exist!'
            );
        }

        /** @var \FqBundle\Entity\Event[] $events */
        $events = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->findBy(['creator' => $user->getId()], ['schedule' => 'DESC']);
        $passedEvents = [];
        $futureEvents = [];
        $currentDate = new \DateTime();
        foreach ($user->getEvents() as $event) {
            if ($event->getSchedule() > $currentDate) {
                $futureEvents[] = $event;
            } else {
                $passedEvents[] = $event;
            }
        }

        return $this->render(
            'FqBundle:User:profile.html.twig',
            [
                'createdEvents' => $events,
                'futureEvents' => array_reverse($futureEvents),
                'passedEvents' => array_reverse($passedEvents),
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/join/{id}", name="join_event")
     */
    public function joinAction(Request $request, $id)
    {
        /** @var \FqBundle\Entity\User $user */
        $user = $this->getDoctrine()
            ->getRepository('FqBundle:User')
            ->find($this->getUser()->getId());
        /** @var \FqBundle\Entity\Event $event */
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id ' . $id
            );
        }
        if (!in_array($event, $user->getEvents()->getValues())) {
            $user->addEvent($event);
            $event->addParticipant($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $message = ['text' => 'Вы присоединились к событию!', 'type' => 'success'];
            try {
                $letter = \Swift_Message::newInstance()
                    ->setSubject('К событию ' . $event->getTitle() . ' присоединился ' . $user->getUsername())
                    ->setFrom(['freequest@startup1.freequest.com.ua' => 'Freequest'])
                    ->setTo($event->getCreator()->getEmail())
                    ->setBody($this->renderView('FqBundle:User:contactEmail.html.twig',
                        ['user' => $user, 'event' => $event]), 'text/html');
                $this->get('mailer')->send($letter);
            } catch (\Exception $e) {
                //todo log exceptions
            }
        } else {
            $message = ['text' => 'Вы уже участник события!', 'type' => 'success'];
        }
        $this->get('session')->set('messages', [$message]);
        $redirectUrl = $request->server->get('HTTP_REFERER') ?: $this->generateUrl('event_view', ['id' => $id]);
        return $this->redirect($redirectUrl);
    }

    /**
     * @Route("/leave/{id}", name="leave_event")
     */
    public function leaveAction(Request $request, $id)
    {
        /** @var \FqBundle\Entity\User $user */
        $user = $this->getDoctrine()
            ->getRepository('FqBundle:User')
            ->find($this->getUser()->getId());
        /** @var \FqBundle\Entity\Event $event */
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id ' . $id
            );
        }
        if (in_array($event, $user->getEvents()->getValues())) {
            $user->removeEvent($event);
            $event->removeParticipant($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $message = ['text' => 'Вы покинули событие!', 'type' => 'success'];
        } else {
            $message = ['text' => 'Вы не участвовали в событии!', 'type' => 'success'];
        }
        $this->get('session')->set('messages', [$message]);
        $redirectUrl = $request->server->get('HTTP_REFERER') ?: $this->generateUrl('event_view', ['id' => $id]);
        return $this->redirect($redirectUrl);
    }

    /**
     * @Route("/hide/{id}", name="hide_event")
     */
    public function hideEventAction(Request $request, $id)
    {
        /** @var \FqBundle\Entity\User $user */
        $user = $this->getDoctrine()
            ->getRepository('FqBundle:User')
            ->find($this->getUser()->getId());
        /** @var \FqBundle\Entity\Event $event */
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if (!in_array($event, $user->getHiddenEvents()->getValues())) {
            $user->addHiddenEvent($event);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        $redirectUrl = $request->server->get('HTTP_REFERER') ?: $this->generateUrl('event_view', ['id' => $id]);
        return $this->redirect($redirectUrl);
    }

    /**
     * @Route("/unhide/{id}", name="unhide_event")
     */
    public function unhideEventAction(Request $request, $id)
    {
        /** @var \FqBundle\Entity\User $user */
        $user = $this->getDoctrine()
            ->getRepository('FqBundle:User')
            ->find($this->getUser()->getId());
        /** @var \FqBundle\Entity\Event $event */
        $event = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->find($id);
        if (in_array($event, $user->getHiddenEvents()->getValues())) {
            $user->removeHiddenEvent($event);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        $redirectUrl = $request->server->get('HTTP_REFERER') ?: $this->generateUrl('event_view', ['id' => $id]);
        return $this->redirect($redirectUrl);
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function editUser(Request $request)
    {
        $id = $request->get('id');
        $value = $request->get('value');
        /** @var \FqBundle\Entity\User $user */
        $user = $this->getDoctrine()
            ->getRepository('FqBundle:User')
            ->find($this->getUser()->getId());
        switch ($id) {
            case ('email'):
                if ($user->getEmail() == $value) {
                    return new Response($value);
                }
                $sameEmail= $this->getDoctrine()
                    ->getRepository('FqBundle:User')
                    ->findOneBy(['email' => $value]);
                if (!empty($sameEmail)) {
                    return new Response('Email already used!', 500);
                }
                $emailPattern = '/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/';
                if (!preg_match($emailPattern, $value)) {
                    return new Response('Wrong email format!', 500);
                }
                $user->setEmail($value);
                break;
            case ('username'):
                if ($user->getUsername() == $value) {
                    return new Response($value);
                }
                $sameUsername = $this->getDoctrine()
                    ->getRepository('FqBundle:User')
                    ->findOneBy(['username' => $value]);
                if (!empty($sameUsername)) {
                    return new Response('Username already used!', 500);
                }
                $user->setUsername($value);
                break;
            case ('phone'):
                if ($user->getPhone() == $value) {
                    return new Response($value);
                }
                $samePhone = $this->getDoctrine()
                    ->getRepository('FqBundle:User')
                    ->findOneBy(['phone' => $value]);
                if (!empty($samePhone)) {
                    return new Response('Phone already used!', 500);
                }
                $user->setPhone($value);
                break;
            default:
                break;
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return new Response($value);
    }
}
