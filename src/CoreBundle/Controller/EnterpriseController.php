<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use CoreBundle\Entity\Enterprise;
use CoreBundle\Form\Type\EnterpriseType;

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

        return $this->render('CoreBundle:Enterprise:show.html.twig', [
            'enterprise' => $enterprise,
            'delete_form' => $deleteForm->createView(),
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
        }

        return $this->redirectToRoute('core_enterprise_index');
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

    protected function getRepositoryName()
    {
        return 'CoreBundle:Enterprise';
    }
}
