<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Intervention;
use CoreBundle\Form\Type\InterventionEditType;

/**
 * Intervention controller.
 *
 */
class InterventionController extends CoreController
{
    const NB_INTERVENTIONS_PER_PAGE = 10;

    /**
     * Lists all Intervention entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CoreBundle:Intervention');

        $query = $repo->queryFindAllParent();
        $oprhanIntervention = $repo->findOrphan();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::NB_INTERVENTIONS_PER_PAGE
        );

        return $this->render('CoreBundle:Intervention:index.html.twig', [
            'pagination' => $pagination,
            'orphan_interventions' => $oprhanIntervention,
        ]);
    }

    /**
     * Finds and displays a Intervention entity.
     *
     */
    public function showAction(Intervention $intervention)
    {
        $editForm = $this->createEditForm($intervention);
        $deleteForm = $this->createDeleteForm($intervention);

        $intervention = $this->get('core.handle.intervention')->getFirstParent($intervention);

        return $this->render('CoreBundle:Intervention:show.html.twig', [
            'intervention' => $intervention,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Intervention entity.
     *
     */
    public function editAction(Request $request, Intervention $intervention)
    {
        $deleteForm = $this->createDeleteForm($intervention);
        $editForm = $this->createForm(InterventionEditType::class, $intervention);
        $editForm->handleRequest($request);

        $intervention = $this->get('core.handle.intervention')->getFirstParent($intervention);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($intervention);
            $em->flush();

            $this->addSuccess('core.success.intervention.edit');

            return $this->redirectToRoute('core_intervention_edit', ['id' => $intervention->getId()]);
        }

        return $this->render('CoreBundle:Intervention:edit.html.twig', [
            'intervention' => $intervention,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Intervention entity.
     *
     */
    public function deleteAction(Request $request, Intervention $intervention)
    {
        $form = $this->createDeleteForm($intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($intervention);
            $em->flush();

            $this->addSuccess('core.success.intervention.delete');
        }

        return $this->redirectToRoute('core_intervention_index');
    }

    /**
     * Creates a form to edit a Intervention entity.
     *
     * @param Intervention $intervention The Intervention entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Intervention $intervention)
    {
        $editForm = $this->createForm(InterventionEditType::class, $intervention, [
            'action' => $this->generateUrl('core_intervention_edit', ['id' => $intervention->getId()])
        ]);

        return $editForm;
    }

    /**
     * Creates a form to delete a Intervention entity.
     *
     * @param Intervention $intervention The Intervention entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Intervention $intervention)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('core_intervention_delete', ['id' => $intervention->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:Intervention';
    }
}
