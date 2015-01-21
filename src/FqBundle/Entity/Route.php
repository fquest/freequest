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
 * @ORM\Table(name="location")
 * @ORM\HasLifecycleCallbacks
 */
class Location
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
    protected $city;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $longitude;
}
