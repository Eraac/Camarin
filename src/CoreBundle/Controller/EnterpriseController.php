<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Intervention;
use CoreBundle\Entity\Plan;
use CoreBundle\Form\Type\InterventionType;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Enterprise;
use CoreBundle\Form\Type\EnterpriseType;
use CoreBundle\Form\Type\PlanType;

/**
 * Enterprise controller.
 *
 */
class EnterpriseController extends CoreController
{
    /**
     * Lists all Enterprise entities.
     *
     */
    public function indexAction()
    {
        $enterprises = $this->getRepository()->findAll();

        return $this->render('CoreBundle:Enterprise:index.html.twig', [
            'enterprises' => $enterprises,
        ]);
    }

    /**
     * Creates a new Enterprise entity.
     *
     */
    public function newAction(Request $request)
    {
        $enterprise = new Enterprise();
        $form = $this->createForm(EnterpriseType::class, $enterprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($enterprise);
            $em->flush();

            $this->addSuccess("core.success.enterprise.add");

            return $this->redirectToRoute('core_enterprise_show', ['slug' => $enterprise->getSlug()]);
        }

        return $this->render('CoreBundle:Enterprise:new.html.twig', [
            'enterprise' => $enterprise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Enterprise entity.
     *
     */
    public function showAction(Enterprise $enterprise)
    {
        $deleteForm = $this->createDeleteForm($enterprise);
        $currentPlans = $this->getRepository('CoreBundle:Plan')->findCurrentPlans($enterprise);

        return $this->render('CoreBundle:Enterprise:show.html.twig', [
            'enterprise' => $enterprise,
            'add_plan_form' => $this->createPlanForm($enterprise)->createView(),
            'add_intervention_form' => $this->createInterventionForm($enterprise)->createView(),
            'delete_form' => $deleteForm->createView(),
            'current_plans' => $currentPlans,
        ]);
    }

    /**
     * Find and displays Plan entities of an Enterprise entity
     *
     * @param Enterprise $enterprise
     */
    public function showPlanAction(Enterprise $enterprise)
    {
        $plans = $this->getRepository('CoreBundle:Plan')->findByEnterprise($enterprise);
        // TODO add pagination

        return $this->render('CoreBundle:Enterprise:plan.html.twig', [
            'enterprise' => $enterprise,
            'add_plan_form' => $this->createPlanForm($enterprise)->createView(),
            'plans' => $plans,
        ]);
    }

    // TODO add show intervention

    /**
     * Displays a form to edit an existing Enterprise entity.
     *
     */
    public function editAction(Request $request, Enterprise $enterprise)
    {
        $deleteForm = $this->createDeleteForm($enterprise);
        $editForm = $this->createForm(EnterpriseType::class, $enterprise);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getEm();
            $em->persist($enterprise);
            $em->flush();

            $this->addSuccess("core.success.enterprise.edit");

            return $this->redirectToRoute('core_enterprise_edit', ['slug' => $enterprise->getSlug()]);
        }

        return $this->render('CoreBundle:Enterprise:edit.html.twig', [
            'enterprise' => $enterprise,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Enterprise entity.
     *
     */
    public function deleteAction(Request $request, Enterprise $enterprise)
    {
        $form = $this->createDeleteForm($enterprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->remove($enterprise);
            $em->flush();

            $this->addSuccess("core.success.enterprise.delete");
        }

        return $this->redirectToRoute('core_enterprise_index');
    }

    /**
     * Add Plan entity to an Enterprise entity
     *
     * @param Request $request
     * @param Enterprise $enterprise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addPlanAction(Request $request, Enterprise $enterprise)
    {
        $plan = new Plan();
        $form = $this->createPlanForm($enterprise, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($plan);
            $em->flush();

            // TODO handle intervention

            $this->addSuccess("core.success.plan.add");
        }

        return $this->redirectToRoute('core_enterprise_show', ['slug' => $enterprise->getSlug()]);
    }

    /**
     * Add Intervention entity to an Forfait entity
     *
     * @param Request $request
     * @param Enterprise $enterprise
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addIntervention(Request $request, Enterprise $enterprise)
    {
        $intervention = new Intervention();
        $form = $this->createInterventionForm($enterprise, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($intervention);
            $em->flush();

            $this->addSuccess("core.success.intervention.add");
        }

        return $this->redirectToRoute('core_enterprise_show', ['slug' => $enterprise->getSlug()]);
    }

    /**
     * Creates a form to delete a Enterprise entity.
     *
     * @param Enterprise $enterprise The Enterprise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Enterprise $enterprise)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('core_enterprise_delete', ['slug' => $enterprise->getSlug()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Creates a form to add Plan to an Enterprise entity.
     *
     * @param Enterprise $enterprise The Enterprise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPlanForm(Enterprise $enterprise, Plan $plan = null)
    {
        if (is_null($plan)) {
            $plan = new Plan();
        }

        $plan->setEnterprise($enterprise);

        return $this->createForm(PlanType::class, $plan, [
            'action' => $this->generateUrl('core_enterprise_add_plan', ['slug' => $enterprise->getSlug()])
        ]);
    }

    /**
     * Creates a form to add Intervention to an Enterprise entity.
     *
     * @param Enterprise $enterprise The Enterprise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createInterventionForm(Enterprise $enterprise, Intervention $intervention = null)
    {
        if (is_null($intervention)) {
            $intervention = new Intervention();
        }

        return $this->createForm(InterventionType::class, $intervention, [
            'action' => $this->generateUrl('core_enterprise_add_intervention', ['slug' => $enterprise->getSlug()])
        ]);
    }


    protected function getRepositoryName()
    {
        return 'CoreBundle:Enterprise';
    }
}
