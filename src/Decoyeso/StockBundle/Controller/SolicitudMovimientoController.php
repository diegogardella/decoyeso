<?php

namespace Decoyeso\StockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Decoyeso\StockBundle\Entity\SolicitudMovimientoElemento;
use Decoyeso\StockBundle\Entity\SolicitudMovimiento;
use Decoyeso\StockBundle\Form\SolicitudMovimientoType;

use Decoyeso\StockBundle\Entity\MovimientoStock;
use Symfony\Component\HttpFoundation\Response;

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
    
    	$buscador->setSql('SELECT sm FROM StockBundle:SolicitudMovimiento sm join sm.pedido p ORDER BY sm.estado ASC, sm.fechaHoraRequerido ASC, sm.id ASC');
    
    	$opciones=array(
    			"sm_estado"=>array('choice',array("label"=>"Estado",'choices'=>	array(""=>"",1 => 'Solicitud Enviada', 2 => 'Solicitud Procesada', 3 => 'Solicitud Cancelada'))),
    			"sm_numero"=>array(null,array("label"=>"Número")),
    			"p_numero"=>array(null,array("label"=>"Número de Pedido")),
    			"p_nombre"=>array(null,array("label"=>"Nombre de Pedido")),
    			);
    	$buscador->setOpcionesForm($opciones);
    	

    
    	$resultados=$buscador->exeBuscar();
    
    
    	return $this->render('StockBundle:SolicitudMovimiento:admin_list.html.twig', array(
    			'entities' => $resultados["entities"],
    			'formBuscar'=>$resultados["form"]->createView(),
    	));
    }
    
    
    public function solicitudMovimientoPorPedidoAction($pedido){
    
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$pedidoDeSolicitud = $em->getRepository('PedidoBundle:Pedido')->find($pedido);
    
    	$entity = $em->getRepository('StockBundle:SolicitudMovimiento')->findBy(array('pedido'=>$pedido),array('estado'=>'ASC','fechaHoraRequerido'=>'ASC','id'=>'DESC'));
     
    	$elementosYCantidades=$this->getCantidades($pedido);

    	return $this->render('StockBundle:SolicitudMovimiento:admin_list_por_pedido.html.twig', array(
    			'entities' => $entity,
    			'pedido'=>$pedidoDeSolicitud,
    			'arrayElementosCantidades'=>$elementosYCantidades['cantidades'],
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

        $cancelarForm = $this->createCancelarForm($id);

        return $this->render('StockBundle:SolicitudMovimiento:admin_show.html.twig', array(
            'entity'      => $entity,
            'cancelar_form' => $cancelarForm->createView(),
        ));
    }
    
    /**
     * Finds and displays a SolicitudMovimiento entity.
     *
     */
    public function imprimirAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('StockBundle:SolicitudMovimiento')->find($id);    	 
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find SolicitudMovimiento entity.');
    	}
    

    	
    	$html = $this->renderView('StockBundle:SolicitudMovimiento:admin_imprimir.html.twig', array(
    			'entity'      => $entity,    
    	));
    	
    	 /*return $this->render('StockBundle:SolicitudMovimiento:admin_imprimir.html.twig', array(
    	 		'entity'      => $entity,
    	 ));
    	
    	/*
    	 spread
    	*/
    	$pdfGenerator = $this->get('spraed.pdf.generator');
    	 
    	return new Response($pdfGenerator->generatePDF($html),
    			200,
    			array(
    					'Content-Type' => 'application/pdf',
    					'Content-Disposition' => 'inline; filename="Remito -'.$entity->getNumero().'.pdf"'
    			)
    	);
    }
    
    
    public function cambiarEstadoAction($id,$estado){
    
    	$em = $this->getDoctrine()->getEntityManager();
    	$solicitudMovimiento = $em->getRepository('StockBundle:SolicitudMovimiento')->find($id);
    	
    	$solicitudMovimiento->setEstado($estado);
    	$solicitudMovimiento->setUsuarioCerro($this->container->get('security.context')->getToken()->getUser());
    	$solicitudMovimiento->setFechaHoraCierre(new \Datetime());
    	
    	$em->persist($solicitudMovimiento);
    	$em->flush();
    
    	return $this->redirect($this->generateUrl('solicitudmovimiento_show',array('id'=>$solicitudMovimiento->getId())));
    }
    
    
    public function newAction($paramPedido)
    {
    	$em=$this->getDoctrine()->getEntityManager();
        $entity = new SolicitudMovimiento();
        
        $pedido=$em->getRepository('PedidoBundle:Pedido')->find($paramPedido);
        $entity->setPedido($pedido);
        $entity->setDireccionDestino($entity->getPedido()->getProvincia()->getNombre().",".$entity->getPedido()->getDepartamento()->getNombre().", ".$entity->getPedido()->getLocalidad()->getNombre()." - ".$entity->getPedido()->getDireccionBarrio().", ".$entity->getPedido()->getDireccionCalle().", ".$entity->getPedido()->getDireccionNumero());
        $entity->setFechaHoraRequerido(new \DateTime('tomorrow 08:00'));
        
        $elementosYCantidades=$this->getCantidades($paramPedido);
        
        $elementos=$em->createQuery('SELECT e FROM ProductoBundle:Elemento e WHERE e.id IN ('.$elementosYCantidades["idsElementosEnPresupuesto"].')')->getResult();       
        
        $form   = $this->createForm(new SolicitudMovimientoType(), $entity);
        
        return $this->render('StockBundle:SolicitudMovimiento:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'elementos'=>$elementos,
        	'arrayElementosCantidades'=>$elementosYCantidades['cantidades'],
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
           
         $entity->setUsuarioCreo($this->container->get('security.context')->getToken()->getUser());
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
        
        $elementosYCantidades=$this->getCantidades($entity->getPedido()->getId());


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SolicitudMovimiento entity.');
        }

        if($entity->getEstado()!=1){
        	return $this->redirect($this->generateUrl('solicitudmovimiento_show',array('id'=>$entity->getId())));
        }
        
        
        $editForm = $this->createEditForm($entity);
        $cancelarForm = $this->createCancelarForm($id);

        return $this->render('StockBundle:SolicitudMovimiento:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'cancelar_form' => $cancelarForm->createView(),
        	'arrayElementosCantidades'=>$elementosYCantidades['cantidades'],
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
        $entity->setUsuarioCerro($this->container->get('security.context')->getToken()->getUser());
        $entity->setFechaHoraCierre(new \Datetime());
        $em->persist($entity);
            	
    	$movimientoStock=array();
    	$j=0;
    	foreach($form['solicitudMovimientoElementoCantidadEntregada'] as $key =>$value){
    	
    		$elementoSolicitudMovimiento=$em->getRepository('StockBundle:SolicitudMovimientoElemento')->find($key);
    		
    		$movimientoStock[$j]=new MovimientoStock();
    		$movimientoStock[$j]->setElemento($elementoSolicitudMovimiento->getElemento());
    		$movimientoStock[$j]->setCantidad($value);
    		$movimientoStock[$j]->setAccion(2);
    		$movimientoStock[$j]->setMotivo(2);
    		$movimientoStock[$j]->setUsuario($this->container->get('security.context')->getToken()->getUser());
    		$movimientoStock[$j]->setFechaHora(new \DateTime());
    		$movimientoStock[$j]->setObservacion('Se confirmo solicitud y se entrego producto y/o insumo');
    	
    		$em->persist($movimientoStock[$j]);
    		$em->flush();
    		
    		$elementoSolicitudMovimiento->setMovimientoStock($movimientoStock[$j]);
    		$em->persist($elementoSolicitudMovimiento);
    		$em->flush();

    		$j++;
    	
    	}
    	
    	
    	
    	
        return $this->redirect($this->generateUrl('solicitudmovimiento_edit', array('id' => $id)));
        
    }

    /**
     * Deletes a SolicitudMovimiento entity.
     *
     */
    public function cancelarAction($id)
    {
        $form = $this->createCancelarForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            
        	$em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('StockBundle:SolicitudMovimiento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SolicitudMovimiento entity.');
            }            
                        
             
            $entity->setEstado(3);
            $entity->setUsuarioCerro($this->container->get('security.context')->getToken()->getUser());
            $entity->setFechaHoraCierre(new \Datetime());
             
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('solicitudmovimiento_show',array('id'=>$entity->getId())));

         
        }

         return $this->redirect($this->generateUrl('solicitudmovimiento_show',array('id'=>$id)));
    }

    private function createCancelarForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    private function createEditForm($entity)
    {
    	
    	return $this->createFormBuilder(array('pedido' => $entity->getPedido()->getId(),'direccionDestino'=>$entity->getDireccionDestino()))
    	->add('pedido', 'hidden')
    	->add('direccionDestino', 'text',array('label'=>'Destino'))
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
    
    public function getCantidades($paramPedido){
    	
    	$em=$this->getDoctrine()->getEntityManager();
    	$presupuesto=$em->getRepository('PedidoBundle:Presupuesto')->findBy(array('pedido'=>$paramPedido,'estado'=>1));
    	$presupuestoItems=json_decode($presupuesto[0]->getItems(),true);    	
    	
    	$arrayElementosCantidades=array();
    	$idsElementos="0";
    	$i=0;
    	
    	foreach($presupuestoItems as $key=>$value ){
    		
    		if($value["id"]!=''){
    	
    			$elemento=$em->getRepository('ProductoBundle:Elemento')->find($value["id"]);
    			$cla=explode('\\',get_class($elemento));
    			$nombreDeClase=$cla[count($cla)-1];
    	
    			if($nombreDeClase=="Servicio"){
    				 
    				 
    				$servicioProductos=$em->getRepository('ProductoBundle:ServicioProducto')->findByServicio($value["id"]);
    				foreach($servicioProductos as $servicioProducto){
    	
    					$id=$servicioProducto->getProducto()->getId();
    	
    					if(array_key_exists($id,$arrayElementosCantidades)){
    						 
    						$arrayElementosCantidades[$id]["getCantidadPresupuestadaIndirectamenteAprobada"]=$arrayElementosCantidades[$id]["getCantidadPresupuestadaIndirectamenteAprobada"]+($servicioProducto->getProducto()->getCantidadPresupuestadaIndirectamenteAprobada());
    						 
    					}else{
    						 
    						$arrayElementosCantidades[$id]['id']=$servicioProducto->getProducto()->getId();
    						$arrayElementosCantidades[$id]["designacion"]=$servicioProducto->getProducto()->getNombre();
    						$arrayElementosCantidades[$id]["unidad"]=$servicioProducto->getProducto()->getUnidad();
    						 
    						$idsElementos.=",".$id;
    						 
    						$arrayElementosCantidades[$id]["cantidadPresupuesto"]=0;
    						$arrayElementosCantidades[$id]["getCantidadPresupuestadaIndirectamenteAprobada"]=$servicioProducto->getProducto()->getCantidadPresupuestadaIndirectamenteAprobada();
    						$arrayElementosCantidades[$id]["cantidadEnStock"]=$servicioProducto->getProducto()->getCantidadEnStock();
    						$arrayElementosCantidades[$id]["cantidadSolicitada"]=$servicioProducto->getProducto()->getCantidadSolicitadaStock($paramPedido);
    						$arrayElementosCantidades[$id]["cantidadEntregada"]=$servicioProducto->getProducto()->getCantidadEntregadaStockSolicitud($paramPedido);
    						 
    						 
    					}
    	
    					 
    				}
    				 
    				     				 
    				$servicioInsumos=$em->getRepository('ProductoBundle:ServicioInsumo')->findByServicio($value["id"]);
    				foreach($servicioInsumos as $servicioInsumo){
    					 
    					$id=$servicioInsumo->getInsumo()->getId();
    					 
    					if(array_key_exists($id,$arrayElementosCantidades)){
    	
    						$arrayElementosCantidades[$id]["getCantidadPresupuestadaIndirectamenteAprobada"]=$arrayElementosCantidades[$id]["getCantidadPresupuestadaIndirectamenteAprobada"]+($servicioInsumo->getInsumo()->getCantidadPresupuestadaIndirectamenteAprobada());
    	
    					}else{
    	
    						$arrayElementosCantidades[$id]['id']=$servicioInsumo->getInsumo()->getId();
    						$arrayElementosCantidades[$id]["designacion"]=$servicioInsumo->getInsumo()->getNombre();
    						$arrayElementosCantidades[$id]["unidad"]=$servicioInsumo->getInsumo()->getUnidad();
    						 
    						$arrayElementosCantidades[$id]["cantidadPresupuesto"]=0;
    						$arrayElementosCantidades[$id]["getCantidadPresupuestadaIndirectamenteAprobada"]=$servicioInsumo->getInsumo()->getCantidadPresupuestadaIndirectamenteAprobada();
    						$arrayElementosCantidades[$id]["cantidadEnStock"]=$servicioInsumo->getInsumo()->getCantidadEnStock();
    						$arrayElementosCantidades[$id]["cantidadSolicitada"]=$servicioInsumo->getInsumo()->getCantidadSolicitadaStock($paramPedido);
    						$arrayElementosCantidades[$id]["cantidadEntregada"]=$servicioInsumo->getInsumo()->getCantidadEntregadaStockSolicitud($paramPedido);
    						$idsElementos.=",".$id;
    						 
    					}
    					 
    					 
    				}
    				     				    				
    				 
    			}else{
    				 
    				$id=$value["id"];
    				 
    				if(array_key_exists($id,$arrayElementosCantidades)){
    					 
    					$arrayElementosCantidades[$id]["cantidadPresupuesto"]=$arrayElementosCantidades[$id]["cantidadPresupuesto"]+$value["cantidad"];
    					 
    				}else{
    					 
    					$arrayElementosCantidades[$id]['id']=$id;
    					$arrayElementosCantidades[$id]["designacion"]=$value["designacion"];
    					$arrayElementosCantidades[$id]["unidad"]=$value["unidad"];
    	    	
    					$idsElementos.=",".$id;
    	
    					$arrayElementosCantidades[$id]["cantidadPresupuesto"]=$value["cantidad"];
    					$arrayElementosCantidades[$id]["getCantidadPresupuestadaIndirectamenteAprobada"]=0;
    					$arrayElementosCantidades[$id]["cantidadEnStock"]=$elemento->getCantidadEnStock();
    					$arrayElementosCantidades[$id]["cantidadSolicitada"]=$elemento->getCantidadSolicitadaStock($paramPedido);
    					$arrayElementosCantidades[$id]["cantidadEntregada"]=$elemento->getCantidadEntregadaStockSolicitud($paramPedido);
    					 
    				}
    				 
    			}
    	
    		}
    	}
    	
    	
    	return array('idsElementosEnPresupuesto'=>$idsElementos,'cantidades'=>$arrayElementosCantidades);
    	
    }
    
}
