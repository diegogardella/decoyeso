<?php

namespace Decoyeso\StockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\StockBundle\Entity\StockProducto;
use Decoyeso\StockBundle\Form\StockProductoType;

/**
 * StockProducto controller.
 *
 */
class StockProductoController extends Controller
{
    /**
     * Lists all StockProducto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('StockBundle:StockProducto')->findAll();

        return $this->render('StockBundle:StockProducto:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a StockProducto entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('StockBundle:StockProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StockProducto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('StockBundle:StockProducto:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new StockProducto entity.
     *
     */
    public function newAction()
    {
        $entity = new StockProducto();
        $form   = $this->createForm(new StockProductoType(), $entity);

        return $this->render('StockBundle:StockProducto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new StockProducto entity.
     *
     */
    public function createAction()
    {
        $entity  = new StockProducto();
        $request = $this->getRequest();
        $form    = $this->createForm(new StockProductoType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('stockproducto_show', array('id' => $entity->getId())));
            
        }

        return $this->render('StockBundle:StockProducto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing StockProducto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('StockBundle:StockProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StockProducto entity.');
        }

        $editForm = $this->createForm(new StockProductoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('StockBundle:StockProducto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing StockProducto entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('StockBundle:StockProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StockProducto entity.');
        }

        $editForm   = $this->createForm(new StockProductoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('stockproducto_edit', array('id' => $id)));
        }

        return $this->render('StockBundle:StockProducto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a StockProducto entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('StockBundle:StockProducto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find StockProducto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('stockproducto'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
