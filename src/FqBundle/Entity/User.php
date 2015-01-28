<?php
/**
 * Created by PhpStorm.
 * User: sivashchenko
 * Date: 12/6/2014
 * Time: 11:32 PM
 */

namespace FqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $picture;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @var @ORM\Column(type="string", length=250, unique=true)
     */
    protected $email;

    /**
     * @var @ORM\Column(type="string", length=250, unique=true)
     */
    protected $fbid;

    /**
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="participants")
     * @ORM\JoinTable(name="participation")
     */
    protected $events;

    /**
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="hidding")
     * @ORM\JoinTable(name="hidding")
     */
    protected $hiddenEvents;

    /**
     * @var @ORM\Column(type="boolean")
     */
    protected $confirmed;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * Is attending event
     *
     * @param \FqBundle\Entity\Event $event
     * @return bool
     */
    public function isAttending(\FqBundle\Entity\Event $event)
    {
        return $this->events->contains($event);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER', 'ROLE_OAUTH_USER'];
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function equals(UserInterface $user)
    {
        return $user->getUsername() === $this->username;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fbid
     *
     * @param string $fbid
     * @return User
     */
    public function setFbid($fbid)
    {
        $this->fbid = $fbid;

        return $this;
    }

    /**
     * Get fbid
     *
     * @return string 
     */
    public function getFbid()
    {
        return $this->fbid;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add events
     *
     * @param \FqBundle\Entity\Event $events
     * @return User
     */
    public function addEvent(\FqBundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \FqBundle\Entity\Event $events
     */
    public function removeEvent(\FqBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add hiddenEvents
     *
     * @param \FqBundle\Entity\Event $hiddenEvents
     * @return User
     */
    public function addHiddenEvent(\FqBundle\Entity\Event $hiddenEvents)
    {
        $this->hiddenEvents[] = $hiddenEvents;

        return $this;
    }

    /**
     * Remove hiddenEvents
     *
     * @param \FqBundle\Entity\Event $hiddenEvents
     */
    public function removeHiddenEvent(\FqBundle\Entity\Event $hiddenEvents)
    {
        $this->hiddenEvents->removeElement($hiddenEvents);
    }

    /**
     * Get hiddenEvents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHiddenEvents()
    {
        return $this->hiddenEvents;
    }

    /**
     * Set confirmed
     *
     * @param \bool $confirmed
     * @return User
     */
    public function setConfirmed(\bool $confirmed)
    {
        $this->confirmed = $confirmed;
    
        return $this;
    }

    /**
     * Get confirmed
     *
     * @return \bool 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }
}
