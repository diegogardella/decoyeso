<?php

namespace Decoyeso\ObraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ObraBundle\Entity\Obra;
use Decoyeso\ObraBundle\Form\ObraType;

/**
 * Obra controller.
 *
 */
class ObraController extends Controller
{
    /**
     * Lists all Obra entities.
     *
     */
	public function indexAction($pararouting="index")
	{
		$buscador=$this->get("buscador");
		$buscador->setRequest($this->getRequest());
		$buscador->setPararouting($pararouting);
	
		$buscador->setSql('SELECT o FROM ObraBundle:Obra o ORDER BY o.estado ASC, o.fechaCreado DESC');
	
		/*$opciones=array(
				"o_numero"=>array(null,array("label"=>"Número de obra")),
    			
    			);
		
		$buscador->setOpcionesForm($opciones);*/
	
		$resultados=$buscador->exeBuscar();

		return $this->render('ObraBundle:Obra:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
		));
	
	
	}
	
	
	public function newFromPedidoAction($pedido)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$pedidoO = $em->getRepository('PedidoBundle:Pedido')->find($pedido);
	        	
		$obra = New Obra();
		$obra->setNombre($pedidoO->getNombre());
		$obra->setPedido($pedidoO);
		$obra->setEstado(0);
		$em->persist($obra);
    
         //Cambio el estado en el pedido
        $pedidoO->setEstado(5);
        $em->persist($pedidoO);
         
        $em->flush();
	
		$this->get('session')->setFlash('msj_info','La Obra se ha creado correctamente');
	
		return $this->redirect($this->generateUrl('obra_edit',array('id'=>$obra->getId())));
	
	}
	
	/**
	 * Displays a form to create a new Presupuesto entity.
	 *
	 */
	public function newAction($pedido=0)
	{
		$em = $this->getDoctrine()->getEntityManager();
	
		$entity = new Obra();
		
	
		if($pedido!=0){
			$entityPedido=$em->getRepository('PedidoBundle:Pedido')->find($pedido);
			//$entity->setPedido($entityPedido);
		}
	
		$form   = $this->createForm(new ObraType(), $entity);
	
		return $this->render('ObraBundle:Obra:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));	
	}
	
	
	/**
	 * Creates a new Obra entity.
	 *
	 */
	public function createAction()
	{
		$entity  = new Obra();
		$request = $this->getRequest();
		$form    = $this->createForm(new ObraType(), $entity);
		$form->bindRequest($request);
	
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($entity);
			$em->flush();

			$log = $this->get('log');
			$log->create($entity, "Obra Creada");
			
			$this->get('session')->setFlash('msj_info','La Obra se ha creado correctamente');
	
			return $this->redirect($this->generateUrl('obra_edit',array('id'=>$entity->getId())));
	
		}
	
        return $this->render('ObraBundle:Obra:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
	}

    /**
     * Finds and displays a Obra entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ObraBundle:Obra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Obra entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ObraBundle:Obra:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }


    public function editFromPedidoAction($paramPedido)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository('ObraBundle:Obra')->findOneByPedido($paramPedido);
    
    	return $this->redirect($this->generateUrl('obra_edit', array('id' => $entity->getId())));
    }


    /**
     * Displays a form to edit an existing Pedido entity.
     *
     */
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('ObraBundle:Obra')->find($id);
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Obra entity.');
    	}
    
    	$editForm = $this->createForm(new ObraType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('ObraBundle:Obra:admin_edit.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
    	));
    }

    /**
     * Edits an existing Obra entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ObraBundle:Obra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Obra entity.');
        }

        $editForm   = $this->createForm(new ObraType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

             $this->get('session')->setFlash('msj_info','La obra se ha modificado correctamente');
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Obra Actualizada");

            return $this->redirect($this->generateUrl('obra_edit', array('id' => $id)));
        }

        return $this->render('ObraBundle:Obra:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Obra entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ObraBundle:Obra')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Obra entity.');
            }
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Obra Eliminada");

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('obra'));
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
    			'url' => $this->generateUrl('obra_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar la Obra?'
    	));
    
    }
}
