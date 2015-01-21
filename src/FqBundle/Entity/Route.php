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
 * @ORM\Table(name="route")
 */
class Route
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @todo verify if cascade refresh is what we need
     * @ORM\OneToMany(targetEntity="Location", mappedBy="route", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $locations;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add locations
     *
     * @param \FqBundle\Entity\Location $locations
     * @return Route
     */
    public function addLocation(\FqBundle\Entity\Location $locations)
    {
        $locations->setRoute($this);
        $this->locations[] = $locations;
    
        return $this;
    }

    /**
     * Remove locations
     *
     * @param \FqBundle\Entity\Location $locations
     */
    public function removeLocation(\FqBundle\Entity\Location $locations)
    {
        $this->locations->removeElement($locations);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLocations()
    {
        return $this->locations;
    }
}
