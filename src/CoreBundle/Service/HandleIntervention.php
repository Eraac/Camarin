<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\Enterprise;
use CoreBundle\Entity\Intervention;
use CoreBundle\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Registry;

class HandleIntervention
{
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function persistIntervention(Enterprise $enterprise, Intervention $intervention)
    {
        $plan = $this->getNextExpiredAndAvailablePlan($enterprise);

        if (is_null($plan) || $this->enoughTime($plan, $intervention)) {
            $this->persist($enterprise, $intervention, $plan);
        } else {
            $this->splitIntervention($enterprise, $plan, $intervention);
        }
    }

    public function splitIntervention(Enterprise $enterprise, Plan $plan, Intervention $intervention)
    {
        $maxTime = $plan->getTimeLeft();
        $timeLeft = $intervention->getSeconds() - $maxTime;

        if ($maxTime < $intervention->getSeconds()) {
            $intervention->setTime($this->makeDate($maxTime));
        }

        $this->persist($enterprise, $intervention, $plan);

        if ($timeLeft > 0) {
            $nextPlan = $this->getNextExpiredAndAvailablePlan($enterprise);

            $childIntervention = new Intervention();
            $childIntervention->setParent($intervention);
            $childIntervention->setDescription(""); // can not be null
            $childIntervention->setTime($this->makeDate($timeLeft));

            if (is_null($nextPlan)) {
                $this->persist($enterprise, $childIntervention, $nextPlan);
            } else {
                $this->splitIntervention($enterprise, $nextPlan, $childIntervention);
            }
        }
    }

    private function getNextExpiredAndAvailablePlan(Enterprise $enterprise)
    {
        $repo = $this->doctrine->getRepository('CoreBundle:Plan');

        $plans = $repo->findCurrentPlans($enterprise);

        $plan = (!empty($plans)) ? current($plans) : null;

        return $plan;
    }

    private function persist(Enterprise $enterprise, Intervention $intervention, Plan $plan = null)
    {
        $intervention->setPlan($plan);
        $intervention->setEnterprise($enterprise);
        $em = $this->doctrine->getManager();
        $em->persist($intervention);
        $em->flush();
    }

    private function enoughTime(Plan $plan, Intervention $intervention)
    {
        return $plan->getTimeLeft() >= ($intervention->getTime()->getTimestamp() + $intervention->getTime()->getOffset());
    }

    private function makeDate($seconds)
    {
        return new \DateTime('1st january 1970' . $seconds . ' seconds');
    }
}
