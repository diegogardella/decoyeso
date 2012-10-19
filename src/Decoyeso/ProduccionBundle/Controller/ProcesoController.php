<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProduccionBundle\Entity\Proceso;
use Decoyeso\ProduccionBundle\Form\ProcesoType;

/**
 * Proceso controller.
 *
 */
class ProcesoController extends Controller
{
    /**
     * Lists all Proceso entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('DecoyesoProduccionBundle:Proceso')->findAll();

        return $this->render('DecoyesoProduccionBundle:Proceso:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Proceso entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proceso entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Proceso:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Proceso entity.
     *
     */
    public function newAction()
    {
        $entity = new Proceso();
        $form   = $this->createForm(new ProcesoType(), $entity);

        return $this->render('DecoyesoProduccionBundle:Proceso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Proceso entity.
     *
     */
    public function createAction()
    {
        $entity  = new Proceso();
        $request = $this->getRequest();
        $form    = $this->createForm(new ProcesoType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('proceso_show', array('id' => $entity->getId())));
            
        }

        return $this->render('DecoyesoProduccionBundle:Proceso:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Proceso entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proceso entity.');
        }

        $editForm = $this->createForm(new ProcesoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Proceso:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Proceso entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proceso entity.');
        }

        $editForm   = $this->createForm(new ProcesoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('proceso_edit', array('id' => $id)));
        }

        return $this->render('DecoyesoProduccionBundle:Proceso:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Proceso entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Proceso entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('proceso'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
