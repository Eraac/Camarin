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
    const NB_LAST_INTERVENTION = 10;
    const NB_ENTERPRISE_PER_PAGE = 20;
    const NB_PLANS_PER_PAGE = 10;
    const NB_INTERVENTIONS_PER_PAGE = 10;

    /**
     * Lists all Enterprise entities.
     *
     */
    public function indexAction(Request $request)
    {
        $query = $this->getRepository()->queryFindAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::NB_ENTERPRISE_PER_PAGE
        );

        $addForm = $this->createAddForm();

        return $this->render('CoreBundle:Enterprise:index.html.twig', [
            'pagination' => $pagination,
            'add_enterprise_form' => $addForm->createView(),
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
        $editForm = $this->createEditForm($enterprise);
        $deleteForm = $this->createDeleteForm($enterprise);
        $currentPlans = $this->getRepository('CoreBundle:Plan')->findCurrentPlans($enterprise);
        $lastInterventions = $this->getRepository('CoreBundle:Intervention')->lastInterventionByEnterprise($enterprise, self::NB_LAST_INTERVENTION);

        return $this->render('CoreBundle:Enterprise:show.html.twig', [
            'enterprise' => $enterprise,
            'add_plan_form' => $this->createPlanForm($enterprise)->createView(),
            'add_intervention_form' => $this->createInterventionForm($enterprise)->createView(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'current_plans' => $currentPlans,
            'last_interventions' => $lastInterventions,
        ]);
    }

    /**
     * Find and displays Plan entities of an Enterprise entity
     *
     * @param Enterprise $enterprise
     */
    public function showPlanAction(Request $request, Enterprise $enterprise)
    {
        $query = $this->getRepository('CoreBundle:Plan')->queryByEnterprise($enterprise);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::NB_PLANS_PER_PAGE
        );

        return $this->render('CoreBundle:Enterprise:plan.html.twig', [
            'enterprise' => $enterprise,
            'add_plan_form' => $this->createPlanForm($enterprise)->createView(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * Find and displays Plan entities of an Enterprise entity
     *
     * @param Enterprise $enterprise
     */
    public function showInterventionAction(Request $request, Enterprise $enterprise)
    {
        $repo = $this->getRepository('CoreBundle:Intervention');
        $query = $repo->queryByEnterprise($enterprise);
        $orphanInterventions = $repo->findOrphanByEnterprise($enterprise);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::NB_INTERVENTIONS_PER_PAGE
        );

        return $this->render('CoreBundle:Enterprise:intervention.html.twig', [
            'enterprise' => $enterprise,
            'add_intervention_form' => $this->createInterventionForm($enterprise)->createView(),
            'pagination' => $pagination,
            'orphan_interventions' => $orphanInterventions,
        ]);
    }

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

            return $this->redirectToRoute('core_enterprise_show', ['slug' => $enterprise->getSlug()]);
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
            $this->get('core.handle.plan')->persistPlan($enterprise, $plan);

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
    public function addInterventionAction(Request $request, Enterprise $enterprise)
    {
        $intervention = new Intervention();
        $form = $this->createInterventionForm($enterprise, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('core.handle.intervention')->persistIntervention($enterprise, $intervention);

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
     * Creates a form to add a Enterprise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAddForm()
    {
        $enterprise = new Enterprise();
        $form = $this->createForm(EnterpriseType::class, $enterprise, [
            'action' => $this->generateUrl('core_enterprise_new')
        ]);

        return $form;
    }

    /**
     * Creates a form to edit a Enterprise entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Enterprise $enterprise)
    {
        $form = $this->createForm(EnterpriseType::class, $enterprise, [
            'action' => $this->generateUrl('core_enterprise_edit', ['slug' => $enterprise->getSlug()])
        ]);

        return $form;
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
