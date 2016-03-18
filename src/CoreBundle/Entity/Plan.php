<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Plan
 *
 * @ORM\Table(name="plan")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\PlanRepository")
 */
class Plan
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


    public function __construct()
    {
        $this->expireAt = new \DateTime('now + 1 year');
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
}
