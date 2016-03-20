<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\Enterprise;
use CoreBundle\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Registry;

class HandlePlan
{
    private $doctrine;
    private $handleIntervention;

    public function __construct(Registry $doctrine, HandleIntervention $handleIntervention)
    {
        $this->doctrine = $doctrine;
        $this->handleIntervention = $handleIntervention;
    }

    public function persistPlan(Enterprise $enterprise, Plan $plan)
    {
        $interventions = $this->getOrphelinIntervention($enterprise);

        $this->persist($enterprise, $plan);

        foreach($interventions as $intervention) {
            $this->handleIntervention->splitIntervention($enterprise, $plan, $intervention);
            $this->doctrine->getManager()->refresh($plan);

            if ($plan->getTimeLeft() == 0) {
                break;
            }
        }
    }

    private function persist(Enterprise $enterprise, Plan $plan)
    {
        $plan->setEnterprise($enterprise);
        $em = $this->doctrine->getManager();
        $em->persist($plan);
        $em->flush();
    }

    private function getOrphelinIntervention(Enterprise $enterprise)
    {
        $repo = $this->doctrine->getRepository('CoreBundle:Intervention');

        return $repo->getOrphelinIntervention($enterprise);
    }
}
