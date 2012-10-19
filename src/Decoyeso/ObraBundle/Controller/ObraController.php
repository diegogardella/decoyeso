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
	
		$buscador->setSql('SELECT p FROM DecoyesoObraBundle:Obra p order by p.estado asc, p.fechaCreado desc');
	
		$opciones=array(
				"p_numero"=>array(null,array("label"=>"Número de obra")),
    			
    			);
		
		$buscador->setOpcionesForm($opciones);
	
		$resultados=$buscador->exeBuscar();
	
	
		return $this->render('DecoyesoObraBundle:Obra:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
		));
	
	
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
	
		return $html =   $this->render('DecoyesoObraBundle:Obra:admin_new.html.twig', array(
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
	
        return $this->render('DecoyesoObraBundle:Obra:admin_new.html.twig', array(
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

        $entity = $em->getRepository('DecoyesoObraBundle:Obra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Obra entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoObraBundle:Obra:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }




    /**
     * Displays a form to edit an existing Pedido entity.
     *
     */
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoObraBundle:Obra')->find($id);
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Obra entity.');
    	}
    
    	$editForm = $this->createForm(new ObraType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('DecoyesoObraBundle:Obra:admin_edit.html.twig', array(
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

        $entity = $em->getRepository('DecoyesoObraBundle:Obra')->find($id);

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

        return $this->render('DecoyesoObraBundle:Obra:edit.html.twig', array(
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
            $entity = $em->getRepository('DecoyesoObraBundle:Obra')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Obra entity.');
            }

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
