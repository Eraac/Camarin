<?php

namespace CoreBundle\Twig;

use CoreBundle\Entity\Enterprise;
use CoreBundle\Entity\Intervention;
use CoreBundle\Interfaces\TimetableInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Translation\DataCollectorTranslator;

class CoreExtension extends \Twig_Extension
{
    private $doctrine;
    private $translator;

    public function __construct(Registry $doctrine, DataCollectorTranslator $translator)
    {
        $this->doctrine = $doctrine;
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('time_left', [$this, 'timeLeft']),
            new \Twig_SimpleFilter('time', [$this, 'time']),
            new \Twig_SimpleFilter('time_enterprise', [$this, 'timeEnterprise']),
            new \Twig_SimpleFilter('hint', [$this, 'hint'], ['is_safe' => ['html']]),
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

    public function hint($string)
    {
        $hint = $this->translator->trans($string, [], 'hints');

        dump($hint);

        return 'data-hint="' . $hint . '"';
    }

    private function secondsToString($seconds)
    {
        $isNegative = ($seconds < 0);

        if ($isNegative) {
            $seconds *= -1;
        }

        $hours = floor($seconds / 3600);
        $minutes = ($seconds % 3600) / 60;

        if ($minutes == 0) {
            $minutes = "00";
        }

        if ($isNegative) {
            $hours = '-' . $hours;
        }

        return $hours . "h" . $minutes;
    }

    public function getName()
    {
        return 'core_extension';
    }
}
