<?php

namespace Decoyeso\PedidoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\PedidoBundle\Entity\Relevamiento;
use Decoyeso\PedidoBundle\Form\RelevamientoType;

/**
 * Relevamiento controller.
 *
 */
class RelevamientoController extends Controller
{
    /**
     * Lists all Relevamiento entities.
     *
     */
public function indexAction($pararouting="index")
	{
		$buscador=$this->get("buscador");
		$buscador->setRequest($this->getRequest());
		$buscador->setPararouting($pararouting);
	
		$buscador->setSql('SELECT r FROM PedidoBundle:Relevamiento r join r.pedido p order by r.fechaCreado desc');
	
		$opciones=array(
				"r_numero"=>array(null,array("label"=>"Número")),
				"r_descripcion"=>array(null,array("label"=>"Descripción")),
				"r_nombre"=>array(null,array("label"=>"Nombre")),
    			"r_fechaCreado"=>array("date",array("empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"),"format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Creado el')),
    			"p_obra"=>array(null,array("label"=>"Obra")),
    			"p_numero"=>array(null,array("label"=>"Número de pedido")),
    			);
		
		$buscador->setOpcionesForm($opciones);

		$resultados=$buscador->exeBuscar();
	
	
		return $this->render('PedidoBundle:Relevamiento:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
		));
	
	
	}
	
	public function relevamientoPorPedidoAction($pedido){
	
		$em = $this->getDoctrine()->getEntityManager();
	
		$pedidoDelRelevamiento = $em->getRepository('PedidoBundle:Pedido')->find($pedido);
	
		$entity = $em->getRepository('PedidoBundle:Relevamiento')->findBy(array('pedido'=>$pedido),array('fechaCreado'=>'DESC'));
	
		return $this->render('PedidoBundle:Relevamiento:admin_list_por_pedido.html.twig', array(
				'entities' => $entity,
				'pedido'=>$pedidoDelRelevamiento
		));
	
	}

    /**
     * Finds and displays a Relevamiento entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PedidoBundle:Relevamiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relevamiento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PedidoBundle:Relevamiento:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Relevamiento entity.
     *
     */
    public function newAction($pedido=0)
    {
    	$em=$this->getDoctrine()->getEntityManager();
        $entity = new Relevamiento();
        
        if($pedido!=0){
        	$pedidoEntity=$em->getRepository('PedidoBundle:Pedido')->find($pedido);
        	$entity->setPedido($pedidoEntity);
        	$entity->setNombre($pedidoEntity->getNombre());
        }
        
        $form   = $this->createForm(new RelevamientoType(), $entity);

        return $this->render('PedidoBundle:Relevamiento:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Relevamiento entity.
     *
     */
    public function createAction()
    {
        $entity  = new Relevamiento();
        $request = $this->getRequest();
        $form    = $this->createForm(new RelevamientoType(), $entity);
        $form->bindRequest($request);

        
        if ($form->isValid()) {
        	
            $em = $this->getDoctrine()->getEntityManager();
            
            $IdPedido=$entity->getPedido()->getId();
            
            $em->persist($entity);
            $em->flush();
               
            $pedido=$em->getRepository('PedidoBundle:Pedido')->find($IdPedido);
            $pedido->VerificarEstado();
            $em->persist($pedido);
            $em->flush();
            
            $this->get('session')->setFlash('msj_info','El relevamiento se ha creado correctamente');
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Relevamiento Creado");

            return $this->redirect($this->generateUrl('relevamiento_edit',array('id'=>$entity->getId())));
            
        }

        return $this->render('PedidoBundle:Relevamiento:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Relevamiento entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PedidoBundle:Relevamiento')->find($id);
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relevamiento entity.');
        }

        $editForm = $this->createForm(new RelevamientoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PedidoBundle:Relevamiento:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Edits an existing Relevamiento entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
		
        $entity = $em->getRepository('PedidoBundle:Relevamiento')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Relevamiento entity.');
        }

        
        $editForm   = $this->createForm(new RelevamientoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        
        $request = $this->getRequest();
        
       	$idPedidoAnteriro=$entity->getPedido()->getId();    
        $editForm->bindRequest($request);
        $idPedidoNuevo=$entity->getPedido()->getId();
        
        if ($editForm->isValid()) {

        	echo $idPedidoAnteriro." ".$idPedidoNuevo;

            $em->persist($entity);
            $em->flush();
            
            $pedidoAnterior=$em->getRepository('PedidoBundle:Pedido')->find($idPedidoAnteriro);
            $pedidoAnterior->verificarEstado();
            $em->persist($pedidoAnterior);
            
            
            $pedidoNuevo=$em->getRepository('PedidoBundle:Pedido')->find($idPedidoNuevo);
            $pedidoNuevo->verificarEstado();
            $em->persist($pedidoNuevo);
            $em->flush();
            
            $this->get('session')->setFlash('msj_info','El relevamiento se ha modificado correctamente');
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Relevamiento Actualizado");

            return $this->redirect($this->generateUrl('relevamiento_edit', array('id' => $id)));
        }

        return $this->render('PedidoBundle:Relevamiento:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Relevamiento entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PedidoBundle:Relevamiento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Relevamiento entity.');
            }
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Relevamiento Eliminado");
            
           
           	$idPedido=$entity->getPedido()->getId();
           
            
			$em->remove($entity);
            $em->flush();
            
            $pedidoAnterior=$em->getRepository('PedidoBundle:Pedido')->find($idPedido);
            $pedidoAnterior->verificarEstado();
            $em->persist($pedidoAnterior);
            $em->flush();
            

        }

        return $this->redirect($this->generateUrl('relevamiento'));
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
    			'url' => $this->generateUrl('relevamiento_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el relevamiento?'
    	));
    
    }
}
