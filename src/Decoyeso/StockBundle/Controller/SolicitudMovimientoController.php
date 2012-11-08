<?php

namespace Decoyeso\StockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Decoyeso\StockBundle\Entity\SolicitudMovimientoElemento;
use Decoyeso\StockBundle\Entity\SolicitudMovimiento;
use Decoyeso\StockBundle\Form\SolicitudMovimientoType;

/**
 * SolicitudMovimiento controller.
 *
 */
class SolicitudMovimientoController extends Controller
{
    /**
     * Lists all SolicitudMovimiento entities.
     *
     */
 public function indexAction($pararouting="index")
    {
        $buscador=$this->get("buscador");
    	$buscador->setRequest($this->getRequest());
    	$buscador->setPararouting($pararouting);
    
    	$buscador->setSql('SELECT sm FROM StockBundle:SolicitudMovimiento sm join sm.pedido p ORDER BY sm.estado, sm.fechaHoraRequerido ASC');
    
    	/*$opciones=array(
    			"c_tipo"=>array('choice',array("label"=>"Tipo de cliente",'choices'=>	array(""=>"",1 => 'Persona Física', 2 => 'Organización'))),
    			"c_nombre"=>array(null,array("label"=>"Apellido, Nombre")),
    			"c_fechaActualizado"=>array("date", array("empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"),"format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Actualizado el')),
    			"c_fechaCreado"=>array("date",array("empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"),"format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Creado el')),
    			"c_cuitOcuil"=>array(null,array("label"=>"Cuit o Cuil ")),
    			"c_barrio"=>array(null,array("label"=>"Dirección - Barrio")),
    			"c_calle"=>array(null,array("label"=>"Dirección - Calle")),
    			"c_numeroCalle"=>array(null,array("label"=>"Dirección - Número")),
    			"c_numero"=>array(null,array("label"=>"Número")),
    			"c_email"=>array(null,array("label"=>"E-mail")),
    			"c_dni"=>array(null,array("label"=>"DNI"))
    			);
    	$buscador->setOpcionesForm($opciones);*/
    	

    
    	$resultados=$buscador->exeBuscar();
    
    
    	return $this->render('StockBundle:SolicitudMovimiento:admin_list.html.twig', array(
    			'entities' => $resultados["entities"],
    			'formBuscar'=>$resultados["form"]->createView(),
    	));
    }

    /**
     * Finds and displays a SolicitudMovimiento entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('StockBundle:SolicitudMovimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SolicitudMovimiento entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('StockBundle:SolicitudMovimiento:admin_show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }
    
    
    /**
     * Finds and displays a SolicitudMovimiento entity.
     *
     */
    public function showPorPedidoAction($paramPedido)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('StockBundle:SolicitudMovimiento')->findOneByPedido($paramPedido);
    
    	return $this->redirect($this->generateUrl('solicitudmovimiento_show', array('id' => $entity->getId())));

    }
    
    
    /**
     * Displays a form to create a new SolicitudMovimiento entity.
     *
     */
    
    
    public function newVentaDirectaAction($paramPedido)
    {
    	$em=$this->getDoctrine()->getEntityManager();
    	$entity = new SolicitudMovimiento();
    
    	$pedido=$em->getRepository('PedidoBundle:Pedido')->find($paramPedido);   
    	
    	$presupuesto=$em->getRepository('PedidoBundle:Presupuesto')->findBy(array('pedido'=>$paramPedido,'estado'=>1));
    	    	
    	$valorItems="";
    	$presupuestoItems=json_decode($presupuesto[0]->getItems(),true);
    	
    	foreach($presupuestoItems as $key=>$value ){
    	  if($value["id"]!=''){
    	  	$valorItems.=";".$value["id"]."@".$value["cantidad"]."@".$value["unidad"]."@".$value["designacion"];
    	  }
    	}    		    	
    	
    	$form   = $this->createForm(new SolicitudMovimientoType(), $entity);
    
    	return $this->render('StockBundle:SolicitudMovimiento:admin_new_venta_directa.html.twig', array(
    			'entity' => $entity,
    			'form'   => $form->createView(),
    			'presupuestoItems'=>$presupuestoItems,
    			'valorItems'=>$valorItems
    	));
    }
    
    
    public function newAction($paramPedido)
    {
    	$em=$this->getDoctrine()->getEntityManager();
        $entity = new SolicitudMovimiento();
        
        $pedido=$em->getRepository('PedidoBundle:Pedido')->find($paramPedido);
        $entity->setPedido($pedido);
        
        $elementos=$em->getRepository('ProductoBundle:Elemento')->findAll();
        
        $form   = $this->createForm(new SolicitudMovimientoType(), $entity);
        
        return $this->render('StockBundle:SolicitudMovimiento:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'elementos'=>$elementos,        		
        ));
    }

    /**
     * Creates a new SolicitudMovimiento entity.
     *
     */
    public function createAction()
    {
        $entity  = new SolicitudMovimiento();
        $request = $this->getRequest();
        $form    = $this->createForm(new SolicitudMovimientoType(), $entity);
        $form->bindRequest($request);

        $formSolicitudMovimiento=$request->request->get('solicitudmovimiento');
                
         $em = $this->getDoctrine()->getEntityManager();
           
         $entity->setFechaHoraCreado(new \DateTime());
         $entity->setEstado(1);
           
         $entity->getPedido()->setEstado(5);
         
         $em->persist($entity);
         $em->flush();

         $this->gestionarSolicitudMovimiento($formSolicitudMovimiento['elementos'],$entity);
	     $this->get('session')->setFlash('msj_info','La solicitud se ha creado correctamente.');
            
         return $this->redirect($this->generateUrl('solicitudmovimiento_show', array('id' => $entity->getId())));
            
    }

    /**
     * Displays a form to edit an existing SolicitudMovimiento entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('StockBundle:SolicitudMovimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SolicitudMovimiento entity.');
        }

        $editForm = $this->createEditForm($entity->getPedido()->getId());
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('StockBundle:SolicitudMovimiento:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing SolicitudMovimiento entity.
     *
     */
    public function updateAction($id)
    {
    	$em=$this->getDoctrine()->getEntityManager();
    	$request=$this->getRequest();	
    	$form=$request->request->get('form');
    	
        $entity = $em->getRepository('StockBundle:SolicitudMovimiento')->find($id);
        $entity->getPedido()->setEstado(6);
        $entity->setEstado(2);
        $em->persist($entity);
        
    	foreach($form['solicitudMovimientoElementoCantidadReservada'] as $key =>$value){    		
    		$elementoSolicitudMovimiento=$em->getRepository('StockBundle:SolicitudMovimientoElemento')->find($key);
    		$elementoSolicitudMovimiento->setCantidadReservada($value);
    		$em->persist($elementoSolicitudMovimiento);    		
    	}
    	
    	$em->flush();
    	
        return $this->redirect($this->generateUrl('solicitudmovimiento_edit', array('id' => $id)));
        
    }

    /**
     * Deletes a SolicitudMovimiento entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('StockBundle:SolicitudMovimiento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SolicitudMovimiento entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('solicitudmovimiento'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function createEditForm($pedido)
    {
    	return $this->createFormBuilder(array('pedido' => $pedido))
    	->add('pedido', 'hidden')
    	->getForm()
    	;
    }
    
    public function gestionarSolicitudMovimiento($parametroElementos,$solicitudMovimiento){
    	 
    	$em=$this->getDoctrine()->getEntityManager();
    	$elementos=explode(';',$parametroElementos);
    	 
    	for($i=1;$i<count($elementos);$i++){
    		 
    		$elementoMovimiento[$i]=new SolicitudMovimientoElemento();
    		$elemento=explode('@',$elementos[$i]);
    
    		$objetoElemento=$em->getRepository('ProductoBundle:Elemento')->find($elemento[0]);
    		$elementoMovimiento[$i]->setSolicitudMovimiento($solicitudMovimiento);
    		$elementoMovimiento[$i]->setElemento($objetoElemento);
    		$elementoMovimiento[$i]->setCantidadSolicitada($elemento[1]);
    
    		$em->persist($elementoMovimiento[$i]);
    
    	}
    	 
    	$em->flush();
    
    }
    
}
