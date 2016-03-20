<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Intervention;
use CoreBundle\Form\Type\InterventionType;

/**
 * Intervention controller.
 *
 */
class InterventionController extends CoreController
{
    /**
     * Lists all Intervention entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $interventions = $em->getRepository('CoreBundle:Intervention')->findAllParent();

        return $this->render('CoreBundle:Intervention:index.html.twig', [
            'interventions' => $interventions,
        ]);
    }

    /**
     * Finds and displays a Intervention entity.
     *
     */
    public function showAction(Intervention $intervention)
    {
        $deleteForm = $this->createDeleteForm($intervention);

        // TODO redirect to parent

        return $this->render('CoreBundle:Intervention:show.html.twig', [
            'intervention' => $intervention,
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
        $editForm = $this->createForm(InterventionType::class, $intervention);
        $editForm->handleRequest($request);

        // TODO redirect to parent

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

        return $this->redirectToRoute('intervention_index');
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
