<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProduccionBundle\Entity\Mesa;
use Decoyeso\ProduccionBundle\Form\MesaType;

/**
 * Mesa controller.
 *
 */
class MesaController extends Controller
{
    /**
     * Lists all Mesa entities.
     *
     */
    public function indexAction($pararouting="index")
    {
        $buscador=$this->get("buscador");
        $buscador->setRequest($this->getRequest());
        $buscador->setPararouting($pararouting);
        
        $buscador->setSql('SELECT p FROM DecoyesoProduccionBundle:Mesa p order BY p.id DESC');
        
        $opciones=array(
        		"p_nombre"=>array(null,array("label"=>"Mesa")),
        
        );
        
        $buscador->setOpcionesForm($opciones);
        
        $resultados=$buscador->exeBuscar();
        
        
        return $this->render('DecoyesoProduccionBundle:Mesa:admin_list.html.twig', array(
        		'entities' => $resultados["entities"],
        		'formBuscar'=>$resultados["form"]->createView(),
        ));
    }

    /**
     * Finds and displays a Mesa entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Mesa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mesa entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Mesa:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Mesa entity.
     *
     */
    public function newAction()
    {
        $entity = new Mesa();
        $form   = $this->createForm(new MesaType(), $entity);

        return $this->render('DecoyesoProduccionBundle:Mesa:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Mesa entity.
     *
     */
    public function createAction()
    {
        $entity  = new Mesa();
        $request = $this->getRequest();
        $form    = $this->createForm(new MesaType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            $log = $this->get('log');
            $log->create($entity, "Mesa Creada");
            
            $this->get('session')->setFlash('msj_info','La Mesa se ha creado correctamente');

            return $this->redirect($this->generateUrl('mesa_edit', array('id' => $entity->getId())));
            
        }

        return $this->render('DecoyesoProduccionBundle:Mesa:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Mesa entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Mesa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mesa entity.');
        }

        $editForm = $this->createForm(new MesaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Mesa:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Mesa entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Mesa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Mesa entity.');
        }

        $editForm   = $this->createForm(new MesaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $log = $this->get('log');
            $log->create($entity, "Mesa Actualizada");
            
            $this->get('session')->setFlash('msj_info','La Mesa se ha actualizado correctamente');

            return $this->redirect($this->generateUrl('mesa_edit', array('id' => $id)));
        }

        return $this->render('DecoyesoProduccionBundle:Mesa:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Mesa entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('DecoyesoProduccionBundle:Mesa')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Mesa entity.');
            }
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Mesa Eliminada");

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mesa'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function listDeleteformAction($id)
    {
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:list_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('mesa_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar la mesa?'
    	));
    
    }
}
