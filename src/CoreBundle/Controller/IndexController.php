<?php

namespace CoreBundle\Controller;

class IndexController extends CoreController
{
    const MAX_INTERVENTIONS = 20;
    const MAX_PLANS = 20;

    public function indexAction()
    {
        $lastInterventions = $this->getRepository('CoreBundle:Intervention')->findLastInterventions(self::MAX_INTERVENTIONS);
        $nextExpiredPlans = $this->getRepository('CoreBundle:Plan')->findNextExpiredPlans(self::MAX_PLANS);
        $orphanInterventions = $this->getRepository('CoreBundle:Intervention')->getAllOrphanInterventions();

        return $this->render('CoreBundle:Index:index.html.twig', [
            'last_interventions' => $lastInterventions,
            'next_expired_plans' => $nextExpiredPlans,
            'orphan_interventions' => $orphanInterventions,
        ]);
    }

    protected final function getRepositoryName()
    {
        return '';
    }
}
