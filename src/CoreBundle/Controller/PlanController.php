<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Plan;
use CoreBundle\Form\Type\PlanEditType;

/**
 * Plan controller.
 *
 */
class PlanController extends CoreController
{
    const NB_PLANS_PER_PAGE = 10;

    /**
     * Lists all Plan entities.
     *
     */
    public function indexAction(Request $request)
    {
        $query = $this->getRepository()->queryFindAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::NB_PLANS_PER_PAGE
        );

        return $this->render('CoreBundle:Plan:index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Finds and displays a Plan entity.
     *
     */
    public function showAction(Plan $plan)
    {
        $editForm = $this->createEditForm($plan);
        $deleteForm = $this->createDeleteForm($plan);
        $interventions = $this->getRepository('CoreBundle:Intervention')->findByPlan($plan);

        return $this->render('CoreBundle:Plan:show.html.twig', [
            'plan' => $plan,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'interventions' => $interventions,
        ]);
    }

    /**
     * Displays a form to edit an existing Plan entity.
     *
     */
    public function editAction(Request $request, Plan $plan)
    {
        $deleteForm = $this->createDeleteForm($plan);
        $editForm = $this->createForm(PlanEditType::class, $plan);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($plan);
            $em->flush();

            $this->addSuccess('core.success.plan.edit');

            return $this->redirectToRoute('core_plan_show', ['id' => $plan->getId()]);
        }

        return $this->render('CoreBundle:Plan:edit.html.twig', [
            'plan' => $plan,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Plan entity.
     *
     */
    public function deleteAction(Request $request, Plan $plan)
    {
        $form = $this->createDeleteForm($plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($plan);
            $em->flush();

            $this->addSuccess('core.success.plan.delete');
        }

        return $this->redirectToRoute('core_plan_index');
    }

    /**
     * Creates a form to edit a Plan entity.
     *
     * @param Plan $plan The Plan entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Plan $plan)
    {
        $editForm = $this->createForm(PlanEditType::class, $plan, [
            'action' => $this->generateUrl('core_plan_edit', ['id' => $plan->getId()])
        ]);

        return $editForm;
    }

    /**
     * Creates a form to delete a Plan entity.
     *
     * @param Plan $plan The Plan entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Plan $plan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('core_plan_delete', ['id' => $plan->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Plan';
    }
}
