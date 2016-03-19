<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use CoreBundle\Interfaces\TimetableInterface;

/**
 * Plan
 *
 * @ORM\Table(name="plan")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\PlanRepository")
 */
class Plan implements TimetableInterface
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
     * @ORM\Column(name="expireAt", type="date")
     * @Assert\Date()
     */
    private $expireAt;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var CoreBundle\Entity\Enterprise
     *
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Enterprise")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Valid
     */
    private $enterprise;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection()
     *
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Intervention", mappedBy="plan")
     */
    private $interventions;


    public function __construct()
    {
        $this->expireAt = new \DateTime('now + 1 year');
        $this->interventions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Plan
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
     * Set expireAt
     *
     * @param \DateTime $expireAt
     *
     * @return Plan
     */
    public function setExpireAt($expireAt)
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    /**
     * Get expireAt
     *
     * @return \DateTime
     */
    public function getExpireAt()
    {
        return $this->expireAt;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Plan
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
     * Set enterprise
     *
     * @param \CoreBundle\Entity\Enterprise $enterprise
     *
     * @return Plan
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

    public function isExpired()
    {
        $now = new \DateTime();

        return $now->getTimestamp() > $this->expireAt->getTimestamp();
    }

    /**
     * Add intervention
     *
     * @param \CoreBundle\Entity\Intervention $intervention
     *
     * @return Plan
     */
    public function addIntervention(\CoreBundle\Entity\Intervention $intervention)
    {
        $this->interventions[] = $intervention;

        return $this;
    }

    /**
     * Remove intervention
     *
     * @param \CoreBundle\Entity\Intervention $intervention
     */
    public function removeIntervention(\CoreBundle\Entity\Intervention $intervention)
    {
        $this->interventions->removeElement($intervention);
    }

    /**
     * Get interventions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterventions()
    {
        return $this->interventions;
    }

    public function getTimeLeft()
    {
        // TODO optimize ?
        $seconds = -$this->time->getOffset();

        foreach ($this->interventions as $intervention) {
            $seconds += $intervention->getTime()->getTimestamp() + $intervention->getTime()->getOffset();
        }

        $seconds = $this->time->getTimestamp() - $seconds;

        return $seconds;
    }
}
