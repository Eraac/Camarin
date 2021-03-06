<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Intervention
 *
 * @ORM\Table(name="intervention")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\InterventionRepository")
 */
class Intervention
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time")
     * @Assert\Time()
     */
    private $time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     * @Assert\Date()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var Plan
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Plan", inversedBy="interventions")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     */
    private $plan;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Intervention", mappedBy="parent")
     * @Assert\Valid
     */
    private $children;

    /**
     * @var Intervention
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Intervention", inversedBy="children")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Valid
     */
    private $parent;

    /**
     * @var CoreBundle\Entity\Enterprise
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Enterprise")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Valid
     */
    private $enterprise;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date = new \DateTime();
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Intervention
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Intervention
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        if (!is_null($this->parent)) {
            return $this->parent->getDate();
        }

        return $this->date;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Intervention
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
        if (!is_null($this->parent)) {
            return $this->parent->getDescription();
        }

        return $this->description;
    }

    /**
     * Set plan
     *
     * @param \CoreBundle\Entity\Plan $plan
     *
     * @return Intervention
     */
    public function setPlan(\CoreBundle\Entity\Plan $plan = null)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return \CoreBundle\Entity\Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set parent
     *
     * @param \CoreBundle\Entity\Intervention $parent
     *
     * @return Intervention
     */
    public function setParent(\CoreBundle\Entity\Intervention $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CoreBundle\Entity\Intervention
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getSeconds($realTime = false)
    {
        $seconds = $this->time->getTimestamp() + $this->time->getOffset();

        if (!$realTime && !$this->children->isEmpty()) {
            foreach ($this->children as $child) {
                $seconds += $child->getSeconds();
            }
        }

        return $seconds;
    }

    /**
     * Set enterprise
     *
     * @param \CoreBundle\Entity\Enterprise $enterprise
     *
     * @return Intervention
     */
    public function setEnterprise(\CoreBundle\Entity\Enterprise $enterprise = null)
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * Get enterprise
     *
     * @return \CoreBundle\Entity\Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * Add child
     *
     * @param \CoreBundle\Entity\Intervention $child
     *
     * @return Intervention
     */
    public function addChild(\CoreBundle\Entity\Intervention $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \CoreBundle\Entity\Intervention $child
     */
    public function removeChild(\CoreBundle\Entity\Intervention $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }
}
