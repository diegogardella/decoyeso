<?php

namespace Decoyeso\PedidoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\PedidoBundle\Entity\Presupuesto;
use Decoyeso\PedidoBundle\Form\PresupuestoType;
use Symfony\Component\HttpFoundation\Response;



/**
 * Presupuesto controller.
 *
 */
class PresupuestoController extends Controller
{
	
	
	public function getItemsAction()
	{
		
		$string = $this->getRequest()->get("term");
		
		$em = $this->getDoctrine()->getEntityManager();
	
		$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
				WHERE  p.nombre LIKE :p_nombre
				ORDER BY p.nombre ASC
				');
		
		$query->setParameters(array(
				'p_nombre' => "%".$string."%",
		));
		
		$productos = $query->getResult();
		
		$query = $em->createQuery('SELECT p FROM ProductoBundle:Insumo p
				WHERE  p.nombre LIKE :p_nombre
				ORDER BY p.nombre ASC
				');
		
		$query->setParameters(array(
				'p_nombre' => "%".$string."%",
		));
		
		$insumos = $query->getResult();
		
		$query = $em->createQuery('SELECT p FROM ProductoBundle:Servicio p
				WHERE  p.nombre LIKE :p_nombre
				ORDER BY p.nombre ASC
				');
		
		$query->setParameters(array(
				'p_nombre' => "%".$string."%",
		));
		
		$servicios = $query->getResult();
		
		

		 
		 foreach($productos as $p):
		 		 $items[] = array(
		 			"designacion" => $p->getNombre(),
		 			"unidad" => $p->getUnidad(),
		 			"precio" => $p->getPrecio(),
		 		 	"id" => $p->getId(),
		 		);
		 endforeach;
		 foreach($insumos as $p):
		 $items[] = array(
		 		"designacion" => $p->getNombre(),
		 		"unidad" => $p->getUnidad(),
		 		"precio" => $p->getPrecio(),
		 		"id" => $p->getId(),
		 );
		 endforeach;
		 foreach($servicios as $p):
		 $items[] = array(
		 		"designacion" => $p->getNombre(),
		 		"unidad" => $p->getUnidad(),
		 		"precio" => $p->getPrecio(),
		 		"id" => $p->getId(),
		 );
		 endforeach;
		 
		
		$return = json_encode(array(), JSON_FORCE_OBJECT);
		if (isset($items)) $return = json_encode($items, JSON_FORCE_OBJECT);
		//print_r($return);
		
		

		//exit();
			
		return new Response($return,200,array('Content-Type'=>'application/json'));
		
	}
	
	
    /**
     * Lists all Presupuesto entities.
     *
     */
	public function indexAction($pararouting="index")
    {
    	$buscador=$this->get("buscador");
    	$buscador->setRequest($this->getRequest());
    	$buscador->setPararouting($pararouting);
    	
    	$buscador->setSql('SELECT pre FROM PedidoBundle:Presupuesto pre join pre.pedido p order by pre.fechaCreado desc, pre.id desc');
    	
    	$opciones=array(
    			"pre_numero"=>array(null,array("label"=>"Número")),
    			"pre_fechaCreado"=>array("date",array("label"=>"Creado el","format"=>'d-m-Y','pattern' => '{{ day }}{{ month }}{{ year }}',"empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"))),
    			"p_obra"=>array(null,array("label"=>"Obra")),
    			"p_numero"=>array(null,array("label"=>"Número de pedido")),
    	);
    	
    	$buscador->setOpcionesForm($opciones);
    	
    	$resultados=$buscador->exeBuscar();
    	
    	return $this->render('PedidoBundle:Presupuesto:admin_list.html.twig', array(
    			'entities' => $resultados["entities"],
    			'formBuscar'=>$resultados["form"]->createView(),
    	));
    
    
    }

    public function presupuestoPorPedidoAction($pedido){
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$pedidoDelPresupuesto = $em->getRepository('PedidoBundle:Pedido')->find($pedido);
    
    	$entity = $em->getRepository('PedidoBundle:Presupuesto')->findBy(array('pedido'=>$pedido),array('fechaCreado'=>'DESC'));
    
    	return $this->render('PedidoBundle:Presupuesto:admin_list_por_pedido.html.twig', array(
    			'entities' => $entity,
    			'pedido'=>$pedidoDelPresupuesto
    	));
    
    }
    
    /**
     * Displays a form to create a new Presupuesto entity.
     *
     */
    public function newAction($pedido=0)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
        $entity = new Presupuesto();
        $entity->setMostrarColumnas(array(0,1,2,3,6)); 
        
        if($pedido!=0){
        	$entityPedido=$em->getRepository('PedidoBundle:Pedido')->find($pedido);
        	$entity->setPedido($entityPedido);
        	$entity->setNombre($entityPedido->getNombre());
        }
        
        $form   = $this->createForm(new PresupuestoType(), $entity);
        
        return $html =   $this->render('PedidoBundle:Presupuesto:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
     * Displays a form to create a new Presupuesto entity.
     *
     */
    public function copiaAction($id)
    {
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$entity = $em->getRepository('PedidoBundle:Presupuesto')->find($id);
    	
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Presupuesto entity.');
    	}

    	$entity->setItems(json_decode($entity->getItems(), true));
    	
    	$form = $this->createForm(new PresupuestoType(), $entity);
    	
    	
        return $this->render('PedidoBundle:Presupuesto:admin_copia.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    
    }

    /**
     * Creates a new Presupuesto entity.
     *
     */
    public function createAction()
    {
        $entity  = new Presupuesto();
        $request = $this->getRequest();
        
        $form    = $this->createForm(new PresupuestoType(), $entity);
        $form->bindRequest($request);
        
        
        $numFilas = $request->get("numFilas");
       
        for($i=0; $i<$numFilas; $i++):

        	if (!$request->request->has("designacion_$i")) 
        	continue;
    
       		$items[$i]['check'] = $request->get("check_$i");
       		$items[$i]['id'] = $request->get("id_$i");
        	$items[$i]['designacion'] = $request->get("designacion_$i");
    	    $items[$i]['unidad'] = $request->get("unidad_$i");
  	        $items[$i]['cantidad'] = $request->get("cantidad_$i");
    	    $items[$i]['precioUnitario'] = $request->get("precioUnitario_$i");
    	    $items[$i]['precioVtaSinIva'] = $request->get("precioVtaSinIva_$i");
    	    $items[$i]['precioVtaConIva'] = $request->get("precioVtaConIva_$i");
   		    $items[$i]['precioTotal'] = $request->get("precioTotal_$i");
        endfor;
        

        

        $entity->setItems(json_encode($items));

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $IdPedido=$entity->getPedido()->getId();
            $em->persist($entity);
            $em->flush();
			
            $log = $this->get('log');
            $log->create($entity, "Presupuesto Creado");

            $pedido=$em->getRepository('PedidoBundle:Pedido')->find($IdPedido);
            $pedido->VerificarEstado();
            $em->persist($pedido);
            $em->flush();
            
            
	     	$this->get('session')->setFlash('msj_info','El presupuesto se ha creado correctamente');

            return $this->redirect($this->generateUrl('presupuesto_edit',array('id'=>$entity->getId())));
        }

        return $this->render('PedidoBundle:Presupuesto:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
 
    
    
    public function imprimirAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('PedidoBundle:Presupuesto')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Presupuesto entity.');
    	}
    
    
    	$entity->setItems(json_decode($entity->getItems(), true));
    	
    	$editForm = $this->createForm(new PresupuestoType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    	
    	
    	$html = $this->renderView('PedidoBundle:Presupuesto:admin_imprimir_presupuesto.html.twig', array(
    			'entity'      => $entity,
    			'hoy'=>new \Datetime(),
    	));
    	/*
    	return $this->render('PedidoBundle:Presupuesto:admin_imprimir_presupuesto.html.twig', array(
    			'entity'      => $entity,
    	));
    	*/
    	/*
    	 spread
    	*/
    	$pdfGenerator = $this->get('spraed.pdf.generator');
    	
    	return new Response($pdfGenerator->generatePDF($html),
    			200,
    			array(
    					'Content-Type' => 'application/pdf',
    					'Content-Disposition' => 'inline; filename="Presupuesto-'.$entity->getNumero().'.pdf"'
    			)
    	);
    	
    	
        
    }

    /**
     * Displays a form to edit an existing Presupuesto entity.
     *
     */
    public function editAction($id)
    {
    	
    	$em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PedidoBundle:Presupuesto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presupuesto entity.');
        }
                
        $entity->setItems(json_decode($entity->getItems(), true));
		        
        $editForm = $this->createForm(new PresupuestoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $aprobarForm = $this->createAprobarForm($id,1);
        
       
        return $this->render('PedidoBundle:Presupuesto:admin_show.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        	'aprobar_form' => $aprobarForm->createView(),
        ));
    }
    
    
    /**
     * Aprueba Presupuesto.
     *
     */
    public function aprobarAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('PedidoBundle:Presupuesto')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Presupuesto entity.');
    	}

        $request = $this->getRequest();
        $form = $request->request->get("form");
        
        $aprobarForm = $this->createAprobarForm($id,$form["tipo"]);
       		
        
        $aprobarForm->bindRequest($request);

        if ($aprobarForm->isValid()) {
        	
        	$presupuestos = $em->getRepository('PedidoBundle:Presupuesto')->findByPedido($entity->getPedido()->getId());

        	$IdPedido=$entity->getPedido()->getId();
        	
        	foreach($presupuestos as $p):
        		$p->setEstado(2);
        		$em->persist($p);
        	endforeach;

        	$entity->setEstado(1);
        	$em->persist($entity);
        	
        	$pedido=$em->getRepository('PedidoBundle:Pedido')->find($IdPedido);
        	$pedido->VerificarEstado();
        	$em->persist($pedido);
        	$em->flush();
        	
            
            //Cambio el estado en el pedido
            $entity->getPedido()->setEstado(4);
        	
        	$em->flush();

            $this->get('session')->setFlash('msj_info','El presupuesto fue aprobado');
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Presupuesto aprobado");

            return $this->redirect($this->generateUrl('presupuesto_edit', array('id' => $id)));
        }

        return $this->render('PedidoBundle:Presupuesto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        	'aprobar_form' => $aprobarForm->createView(),
        ));
    }
    
    
    private function createAprobarForm($id, $tipo)
    {
    	//$tipo = 0 --> Con Obra
    	//$tipo = 1 --> Vta Directa 
    	return $this->createFormBuilder(array('id' => $id, 'tipo' => $tipo))
    	->add('id', 'hidden')
    	->add('tipo', 'hidden')
    	->getForm()
    	;
    }



    /**
     * Deletes a Presupuesto entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('PedidoBundle:Presupuesto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Presupuesto entity.');
            }
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Presupuesto Eliminado");

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('presupuesto'));
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
    			'url' => $this->generateUrl('presupuesto_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el presupuesto?'
    	));
    
    }
    
 
    /**
     * ARREGLAR BD PARA AGREGAR LOS ITEMS
     *
     */
    public function arreglarAction($id)
    {
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entities = $em->getRepository('PedidoBundle:Presupuesto')->findAll();
    	
    	
    	foreach( $entities as $entity):
    	
    	
	    	if (!$entity) {
	    	//	throw $this->createNotFoundException('Unable to find Presupuesto entity.');
	    	break;
	    	}
	    	
	        //ARREGLO ITEMS    
	    	$entity->setItems(json_decode($entity->getItems(), true));
	     	
	    	$items = $entity->getItems();
	    	
	    	$c = count($items);
	    	
	    	for ($r=0; $r<$c; $r++):
	    		$items[$r]['check']= null;
	    		$items[$r]['precioVtaSinIva']= "";
	    		$items[$r]['precioVtaConIva']= "";
	    		
	    		$i = $items[$r];
	    		
	    		foreach ($i as $k => $ii):
	    			echo $k.": ".$ii."- ";
	    		endforeach;
	    		echo "<br>";
	    		/**/
	    	endfor;
	    	
	    	$entity->setItems(json_encode($items));
	    	//ARREGLO ITEMS
	    	
	    	//ARREGLO CHECKS DE COLUMNAS
	    	$entity->setMostrarColumnas(array(0,1,2,3,6));
	    	
	    	//ARREGLO NOMBRE
	    	$entity->setNombre($entity->getNumero());
	    	
	    
	    	$em = $this->getDoctrine()->getEntityManager();
	    	$em->persist($entity);
	    	$em->flush();
	    	
        endforeach;
    }
    

    
    
    
    
    
}
