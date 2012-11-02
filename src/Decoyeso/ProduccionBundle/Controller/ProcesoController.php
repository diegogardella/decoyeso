<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProduccionBundle\Entity\Proceso;
use Decoyeso\ProduccionBundle\Form\ProcesoType;
use Decoyeso\ProduccionBundle\Form\ProcesoFinalizarType;

use Decoyeso\ProduccionBundle\Entity\ProcesoProducto;

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
	public function indexAction($pararouting="index")
	{
		$buscador=$this->get("buscador");
		$buscador->setRequest($this->getRequest());
		$buscador->setPararouting($pararouting);
	
		$buscador->setSql('SELECT p FROM DecoyesoProduccionBundle:Proceso p order BY p.id DESC');
	
		$opciones=array(
				"p_numero"=>array(null,array("label"=>"Número de Proceso")),
    			
    			);
		
		$buscador->setOpcionesForm($opciones);
	
		$resultados=$buscador->exeBuscar();
	
	
		return $this->render('DecoyesoProduccionBundle:Proceso:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
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
    
    	$editForm = $this->createForm(new ProcesoType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	//Traigo productos
    	$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
    			ORDER BY p.nombre ASC
    			');
    	$productos = $query->getResult();
    	
    	//Traigo productos del proceso
    	$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
    			WHERE prp.proceso = :po_id
    			');
    	$query->setParameters(array(
    			'po_id' => $id,
    	));
    	$procesoProducto = $query->getResult();
    	

    	 
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_show.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'procesoProducto'      => $procesoProducto,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
    	));
    }

    /**
     * Displays a form to create a new Proceso entity.
     *
     */
	public function newAction()
	{
		$em = $this->getDoctrine()->getEntityManager();
	
		$entity = new Proceso();
		$entity->setFechaInicio(new \DateTime('today'));
		
		$form   = $this->createForm(new ProcesoType(), $entity);
		
		$em = $this->getDoctrine()->getEntityManager();
		
		$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
				ORDER BY p.nombre ASC
				');
		
		$productos = $query->getResult();
	
		return $html =   $this->render('DecoyesoProduccionBundle:Proceso:admin_new.html.twig', array(
				'entity' => $entity,
				'productos' => $productos,
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
		$em = $this->getDoctrine()->getEntityManager();
		
		if ($form->isValid()) {
			
			$em->persist($entity);
			$em->flush();
			
			$this->agregarProdutosAlProceso($entity);
			
			//exit();

			$log = $this->get('log');
			$log->create($entity, "Proceso Creado");
			
			$this->get('session')->setFlash('msj_info','El Proceso se ha creado correctamente');
	
			return $this->redirect($this->generateUrl('proceso_show',array('id'=>$entity->getId())));
	
		}
	
        return $this->render('DecoyesoProduccionBundle:Proceso:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
	}
	
	public function agregarProdutosAlProceso($entity) {
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		$numFilas = $request->get("numFilas");
		/*
		$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:Proceso prp
				WHERE prp.
				');
		*/
		$productos = $em->getRepository('DecoyesoProduccionBundle:ProcesoProducto')->findByProceso($entity->getId());
		//echo count ($productos);
		foreach ($productos as $p):
			$em->remove($p);
		endforeach;
		
		$em->flush();
		
		//exit();

		for($i=0; $i<$numFilas; $i++):
			if (!$request->request->has("designacion_$i"))	continue;
			if (trim($request->get("cantidad_$i")) == "")	continue;
			
			$items[$i]['designacion'] = $request->get("designacion_$i");
			$items[$i]['cantidad'] = $request->get("cantidad_$i");
			$items[$i]['cantidad_producida'] = 0;
		endfor;
		
		
		
		if (isset($items)):
			foreach ($items as $item):
				$producto = $em->getRepository('ProductoBundle:Producto')->findOneById($item['designacion']);
				if (!$producto) continue;
				
				$procesoProducto = new ProcesoProducto();
				$procesoProducto->setProceso($entity);
				$procesoProducto->setProducto($producto);
				$procesoProducto->setCantidad($item['cantidad']);
				$procesoProducto->setCantidadProducida($item['cantidad_producida']);
				$em->persist($procesoProducto);
			endforeach;
		endif;
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
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	//Traigo productos
    	$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
    			ORDER BY p.nombre ASC
    			');
    	$productos = $query->getResult();
    	
    	//Traigo productos del proceso
    	$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
    			WHERE prp.proceso = :po_id
    			');
    	$query->setParameters(array(
    			'po_id' => $id,
    	));
    	$procesoProducto = $query->getResult();
    	

    	 
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_edit.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'procesoProducto'      => $procesoProducto,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
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
            throw $this->createNotFoundException('Unable to find Obra entity.');
        }

        $editForm   = $this->createForm(new ProcesoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
        	
        	//$this->agregarProdutosAlProceso($entity);
            $em->persist($entity);
            $em->flush();
            
            $this->agregarProdutosAlProceso($entity);

             $this->get('session')->setFlash('msj_info','El proceso se ha modificado correctamente');
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Proceso Actualizado");

            return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
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
            
            //Traigo productos del proceso
            $query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
            		WHERE prp.proceso = :po_id
            		');
            $query->setParameters(array(
            		'po_id' => $id,
            ));
            $procesoProducto = $query->getResult();
            
            foreach ($procesoProducto as $pp) {
            	$em->remove($pp);
            }

            //LOG
            $log = $this->get('log');
            $log->create($entity, "Proceso Eliminado");
            
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
    
    public function listDeleteformAction($id)
    {
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:list_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('proceso_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el proceso?'
    	));
    }
    
    public function iniciarAction ($id) {
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    	
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Proceso entity.');
    	}
    	$entity->setEstado(1);
    	$entity->setFechaInicio(new \DateTime);
    	$em->flush($entity);
    	
    	return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
    }
    
    
    public function finalizarAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Proceso entity.');
    	}
    
    	$editForm = $this->createForm(new ProcesoFinalizarType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	//Traigo productos
    	$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
    			ORDER BY p.nombre ASC
    			');
    	$productos = $query->getResult();
    
    	//Traigo productos del proceso
    	$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
    			WHERE prp.proceso = :po_id
    			');
    	$query->setParameters(array(
    			'po_id' => $id,
    	));
    	$procesoProducto = $query->getResult();
    
    
    
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_finalizar.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'procesoProducto'      => $procesoProducto,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
    	));
    }

    
    public function finalizarUpdateAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Proceso entity.');
    	}
    	
    	$request = $this->getRequest();
    	
    	//Traigo productos del proceso
    	$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
    			WHERE prp.proceso = :po_id
    			');
    	$query->setParameters(array(
    			'po_id' => $id,
    	));
    	$procesoProducto = $query->getResult();
    	
    	foreach ($procesoProducto as $pp):
    		echo $pp->getCantidad()."<br>";
    	if ($request->request->has("cantidadProducida_".$pp->getProducto()->getId())) {
    		
    		echo $request->get("cantidadProducida_".$pp->getProducto());
    		$pp->setCantidadProducida($request->get("cantidadProducida_".$pp->getProducto()->getId()));
    	}
    	else {
    		$pp->setCantidadProducida(0);
    	}

    	$em->persist($entity);
    	endforeach;
    	$entity->setEstado(2);
    	$entity->setFechaFin(new \DateTime);
    	$em->flush($entity);
    	
    
    	return $this->redirect($this->generateUrl('proceso_asignar', array('id' => $id)));
    
    

    }
    

    
    public function asignarProductosSecadoresAction ($id) {
        
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Proceso entity.');
    	}
    	
    	$request = $this->getRequest();
    	
    	//Traigo productos del proceso
    	$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
    			WHERE prp.proceso = :po_id
    			');
    	$query->setParameters(array(
    			'po_id' => $id,
    	));
    	$productos = $query->getResult();
    	
    	
    	
    	//traigo secador
    	$secador = $em->getRepository('DecoyesoProduccionBundle:Secador')->find(11);
    	
    	//traigo los lugares Vacios
    	$lugaresSecador = $secador->getLugaresSecador();
    	
    	
    	//Asigno productos a lugares vacios
    	foreach ($productos as $pp):
    		//0 es placa y 1 es moldura
    	
    		//Placa
    		//if ($pp->getProducto()->getTipo() == 0) {
    		
    			for ($i=0; $i<$pp->getCantidadProducida(); $i++):
    				foreach($lugaresSecador as $lS):
    					//me fijo si esta disponible
    					if (!$lS->getDisponible()) {
    						$lS->setDisponible(1);
    						$lS->setProceso($entity);
    						$lS->setFechaAsignado (new \DateTime);
    						$em->persist($lS);
    						break;
    					}
    				endforeach;
    			endfor;
    		
    		//}
    		
    		//Moldura
    		//if ($pp->getProducto()->getTipo() == 1) {
    		
    		//}
    		
    		
    		
		endforeach;
		$em->flush();
    	return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
    }
}
