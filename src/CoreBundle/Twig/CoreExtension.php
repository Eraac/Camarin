<?php

namespace CoreBundle\Twig;

use CoreBundle\Entity\Intervention;
use CoreBundle\Interfaces\TimetableInterface;

class CoreExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('time_left', [$this, 'timeLeft']),
            new \Twig_SimpleFilter('time', [$this, 'time']),
        ];
    }

    public function timeLeft(TimetableInterface $entity)
    {
        $secondLeft = $entity->getTimeLeft();

        return $this->secondsToString($secondLeft);
    }

    public function time(Intervention $intervention)
    {
        $seconds = $intervention->getSeconds();

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
