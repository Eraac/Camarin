<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class CoreController extends Controller
{
    /**
     * @param string $name
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($name = null)
    {
        if (is_null($name)) {
            $name = $this->getRepositoryName();
        }
        return $this->getDoctrine()->getRepository($name);
    }
    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param string $message
     * @return string
     */
    protected function t($message)
    {
        return $this->get('translator')->trans($message);
    }

    /**
     * @param string $message
     */
    protected function addSuccess($message)
    {
        $this->addFlash('success', $this->t($message));
    }

    /**
     * @return string
     */
    abstract protected function getRepositoryName();
}
