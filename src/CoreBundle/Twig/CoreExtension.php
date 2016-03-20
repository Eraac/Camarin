<?php

namespace CoreBundle\Twig;

use CoreBundle\Entity\Enterprise;
use CoreBundle\Entity\Intervention;
use CoreBundle\Interfaces\TimetableInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CoreExtension extends \Twig_Extension
{
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('time_left', [$this, 'timeLeft']),
            new \Twig_SimpleFilter('time', [$this, 'time']),
            new \Twig_SimpleFilter('time_enterprise', [$this, 'timeEnterprise']),
        ];
    }

    public function timeLeft(TimetableInterface $entity)
    {
        $secondLeft = $entity->getTimeLeft();

        return $this->secondsToString($secondLeft);
    }

    public function time(Intervention $intervention, $realTime = false)
    {
        $seconds = $intervention->getSeconds($realTime);

        return $this->secondsToString($seconds);
    }

    public function timeEnterprise(Enterprise $enterprise)
    {
        $seconds = $this->doctrine->getRepository('CoreBundle:Plan')->getSecondsAvailableForEnterprise($enterprise);

        return $this->secondsToString($seconds);
    }

    private function secondsToString($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = ($seconds % 3600) / 60;

        if ($minutes == 0) {
            $minutes = "00";
        }

        return $hours . "h" . $minutes;
    }

    public function getName()
    {
        return 'core_extension';
    }
}
