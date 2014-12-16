<?php

namespace FqBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use \HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

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
    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
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
        $events = $this->getDoctrine()
            ->getRepository('FqBundle:Event')
            ->findBy(['creator' => $user->getId()]);
        return $this->render(
            'FqBundle:User:dashboard.html.twig',
            [
                'createdEvents' => $events,
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        $user = $this->getUser();
        return $this->render(
            'FqBundle:User:profile.html.twig',
            ['user' => $user]
        );
    }

    /**
     * @Route("/join/{id}", name="join_event")
     */
    public function joinAction($id)
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
        $user->addEvent($event);
        $event->addParticipant($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirect($this->generateUrl('event_view', ['id' => $id]));
    }

    /**
     * @Route("/leave/{id}", name="leave_event")
     */
    public function leaveAction($id)
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
        $user->removeEvent($event);
        $event->removeParticipant($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirect($this->generateUrl('event_view', ['id' => $id]));
    }
}
