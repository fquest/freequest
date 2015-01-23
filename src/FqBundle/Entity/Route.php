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
     * @ORM\OneToOne(targetEntity="Location", mappedBy="id", cascade={"persist", "remove"}, orphanRemoval=true))
     * @ORM\JoinColumn(name="startLocation", referencedColumnName="id")
     */
    protected $startLocation;

    /**
     * @ORM\OneToOne(targetEntity="Location", mappedBy="id", cascade={"persist", "remove"}, orphanRemoval=true))
     * @ORM\JoinColumn(name="endLocation", referencedColumnName="id")
     */
    protected $endLocation;

    /**
     * @ORM\Column(type="text")
     */
    protected $route;

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
     * Set route
     *
     * @param string $route
     * @return Route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    
        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set startLocation
     *
     * @param \FqBundle\Entity\Location $startLocation
     * @return Route
     */
    public function setStartLocation(\FqBundle\Entity\Location $startLocation = null)
    {
        $this->startLocation = $startLocation;
    
        return $this;
    }

    /**
     * Get startLocation
     *
     * @return \FqBundle\Entity\Location 
     */
    public function getStartLocation()
    {
        return $this->startLocation;
    }

    /**
     * Set endLocation
     *
     * @param \FqBundle\Entity\Location $endLocation
     * @return Route
     */
    public function setEndLocation(\FqBundle\Entity\Location $endLocation = null)
    {
        $this->endLocation = $endLocation;
    
        return $this;
    }

    /**
     * Get endLocation
     *
     * @return \FqBundle\Entity\Location 
     */
    public function getEndLocation()
    {
        return $this->endLocation;
    }
}
