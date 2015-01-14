<?php
/**
 * Created by PhpStorm.
 * User: sivashchenko
 * Date: 12/6/2014
 * Time: 11:32 PM
 */

namespace FqBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vlabs\MediaBundle\Annotation\Vlabs;
use Symfony\Component\Validator\Constraints as Assert;
use Vlabs\MediaBundle\Entity\BaseFile as VlabsFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ORM\HasLifecycleCallbacks
 */
class Event
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $position;

    /**
     * @todo verify if cascade refresh is what we need
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var VlabsFile
     *
     * @ORM\OneToOne(targetEntity="Image", mappedBy="id", cascade={"persist", "remove"}, orphanRemoval=true))
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image", referencedColumnName="id")
     * })
     *
     * @Vlabs\Media(identifier="image_entity", upload_dir="files/images")
     * @Assert\Valid()
     */
    protected $image;

    /**
     * @todo verify if cascade refresh is what we need
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    protected $creator;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $schedule;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="events")
     */
    protected $participants;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="hiddenEvents")
     */
    protected $hiddenFor;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $views;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Event
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
     * @return Event
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
     * Set creator
     *
     * @param \FqBundle\Entity\User $creator
     * @return Event
     */
    public function setCreator(\FqBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \FqBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set category
     *
     * @param \FqBundle\Entity\Category $category
     * @return Event
     */
    public function setCategory(\FqBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \FqBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set image
     *
     * @param \FqBundle\Entity\Image $image
     * @return Event
     */
    public function setImage(\FqBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \FqBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add participants
     *
     * @param \FqBundle\Entity\User $participants
     * @return Event
     */
    public function addParticipant(\FqBundle\Entity\User $participants)
    {
        $this->participants[] = $participants;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \FqBundle\Entity\User $participants
     */
    public function removeParticipant(\FqBundle\Entity\User $participants)
    {
        $this->participants->removeElement($participants);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Event
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Event
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set schedule
     *
     * @param \DateTime $schedule
     * @return Event
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \DateTime 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Add hiddenFor
     *
     * @param \FqBundle\Entity\User $hiddenFor
     * @return Event
     */
    public function addHiddenFor(\FqBundle\Entity\User $hiddenFor)
    {
        $this->hiddenFor[] = $hiddenFor;

        return $this;
    }

    /**
     * Remove hiddenFor
     *
     * @param \FqBundle\Entity\User $hiddenFor
     */
    public function removeHiddenFor(\FqBundle\Entity\User $hiddenFor)
    {
        $this->hiddenFor->removeElement($hiddenFor);
    }

    /**
     * Get hiddenFor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHiddenFor()
    {
        return $this->hiddenFor;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return Event
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }
}
