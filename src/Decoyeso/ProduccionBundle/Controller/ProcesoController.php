<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProduccionBundle\Entity\Proceso;
use Decoyeso\ProduccionBundle\Form\ProcesoType;
use Decoyeso\ProduccionBundle\Form\ProcesoFinalizarType;

use Decoyeso\ProduccionBundle\Entity\ProcesoProducto;
use Decoyeso\StockBundle\Entity\MovimientoStock;

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
				"p_fechaCreado"=>array("date", array("empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"),"format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Creado el')),
    			);
		
		$buscador->setOpcionesForm($opciones);
	
		$resultados=$buscador->exeBuscar();
		
		return $this->render('DecoyesoProduccionBundle:Proceso:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
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
			
			/* Creo proceso y le asigno los productos */
			$em->persist($entity);
			$this->agregarProdutosAlProceso($entity);
			$em->flush();
			
			$em->persist($entity);
			$em->flush();
			
						
	
	
			$log = $this->get('log');
			$log->create($entity, "Proceso creado");
	
			$this->get('session')->setFlash('msj_info','El proceso se ha creado correctamente.');
	
			return $this->redirect($this->generateUrl('proceso_show',array('id'=>$entity->getId())));
	
		}
	
		return $this->render('DecoyesoProduccionBundle:Proceso:admin_new.html.twig', array(
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
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    	}
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	//Traigo productos
    	$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
    			ORDER BY p.nombre ASC
    			');
    	$productos = $query->getResult();
    
    	//Traigo productos del proceso
    	$productosDelProceso = $this->getProductosDelProceso($id);
    
    	$editForm = $this->createForm(new ProcesoType(), $entity);
    	
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_edit.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'productosDelProceso'      => $productosDelProceso,
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
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    	}
    
    	$editForm   = $this->createForm(new ProcesoType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    
    	$request = $this->getRequest();
    
    	$editForm->bindRequest($request);
    
    	if ($editForm->isValid()) {
    
    		/* Asigno los productos */
    		$em->persist($entity);
    		$this->agregarProdutosAlProceso($entity);
    		$em->flush();
    
			$em->persist($entity);
			$em->flush();
    
    		$this->get('session')->setFlash('msj_info','El proceso se ha modificado correctamente.');
    
    		//LOG
    		$log = $this->get('log');
    		$log->create($entity, "Proceso actualizado");
    
    		return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
    	}
    
    	return $this->render('DecoyesoProduccionBundle:Proceso:edit.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView(),
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
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    	}
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	//Traigo productos
    	$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
    			ORDER BY p.nombre ASC
    			');
    	$productos = $query->getResult();
    
    	//Traigo productos del proceso
    	$productosDelProceso = $this->getProductosDelProceso($entity->getId());
    
    
    	/* Estimación de insumos a consumir*/
    	if ($entity->getEstado()<1) {
    		$insumos = $this->calcularUsoDeInsumos($entity->getId());
    		$entity->setDatosInsumos(json_encode($insumos));
    	}
    
    
    
    	
    
    	$editForm = $this->createForm(new ProcesoType(), $entity);
    
    	$deleteForm = $this->createDeleteForm($entity->getId());
    
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_show.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'productosDelProceso'      => $productosDelProceso,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
    	));
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
    
    public function accionDeleteformAction($id)
    {
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:accion_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('proceso_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el proceso?'
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
    			throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    		}
    
    		$productosDelProceso = $this->getProductosDelProceso($id);
    
    		foreach ($productosDelProceso as $pp) {
    			$em->remove($pp);
    		}
    
    		//LOG
    		$log = $this->get('log');
    		$log->create($entity, "Proceso eliminado");
    
    		$em->remove($entity);
    		$em->flush();
    
    
    	}
    
    	return $this->redirect($this->generateUrl('proceso'));
    }
    

    
    
    public function iniciarAction ($id) 
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    	}
    	
    	/* Estimación de insumos a consumir */
    	$insumos = $this->calcularUsoDeInsumos($entity->getId());
    	$entity->setDatosInsumos(json_encode($insumos));
    	$em->persist($entity);
    	$em->flush();
    	
    	$entity->setEstado(1);
    	$entity->setFechaInicio(new \DateTime);
    	$em->flush($entity);
    	
    	$log = $this->get('log');
    	$log->create($entity, "Proceso iniciado");
    	
    
    	//Me fijo si se inicio el proceso sin insumos
    	if ($datosInsumos = $entity->getDatosInsumos()) {
    	
	    	$insumos = "";
	    	foreach($datosInsumos as $d):
		    	if(!$d["disponible"]) {
		    		$insumos .= " ".$d["nombre"].",";
		    	}
		    endforeach;
		    
	    	if ($insumos!="") {
	    		$log = $this->get('log');
	    		$log->setPrioridad(2);
	    		$log->setPermisos("ROLE_DEPOSITO");
	    		$log->create(false, "El proceso ".$entity." se ha iniciado sin stock necesario de".substr($insumos, 0, -1));
	    	}
	    	
    	}

    	$this->get('session')->setFlash('msj_info','El Proceso se ha iniciado correctamente.');
    
    	return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
    }
    
    
    public function finalizarAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
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
    	$productosDelProceso = $this->getProductosDelProceso($id);
    
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_finalizar.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'productosDelProceso'      => $productosDelProceso,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
    	));
    }
    
    
    public function finalizarUpdateAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    	}
   
		//Traigo productos del proceso
		$productosDelProceso = $this->getProductosDelProceso($entity->getId());
		
		$request = $this->getRequest();
    	foreach ($productosDelProceso as $pp):
	    
	    	if ($request->request->has("cantidadProducida_".$pp->getProducto()->getId())) {
	    		$pp->setCantidadProducida($request->get("cantidadProducida_".$pp->getProducto()->getId()));
	    	}
	    	else {
	    		$pp->setCantidadProducida(0);
	    	}
	    
	    	$em->persist($entity);
    	endforeach;
    	$entity->setEstado(2);
    	$entity->setFechaFin(new \DateTime);
    
    	/* Calulo los insumos consumidos */
    	$insumos = $this->calcularUsoDeInsumos($entity->getId());
    	$entity->setDatosInsumos(json_encode($insumos));
    	$em->persist($entity);
    	$em->flush();
    
    	/* Descuento Insumos de Stock */
    	$datosInsumos = $entity->getDatosInsumos();
    	if ($datosInsumos) {
    		foreach ($datosInsumos as $di) {
    			$insumo = $em->getRepository('ProductoBundle:Insumo')->find($di["id"]);
    			$this->descontarInsumos($insumo, $di["cantidadProducida"]);
    		}	
    	}

    	
    	$log = $this->get('log');
    	$log->setPrioridad(1);
    	$log->create($entity, "Proceso Finalizado");
    	
    	$this->get('session')->setFlash('msj_info','El proceso ha finalizado correctamente.');
    
    
    	return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
    
    
    }
    
    
    
    public function asignarProductosAlStockAction($id) {
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    	}
    	
    	
    	//Traigo productos
    	$query = $em->createQuery('SELECT p FROM ProductoBundle:Producto p
    			ORDER BY p.nombre ASC
    			');
    	$productos = $query->getResult();
    	
		//Traigo productos del proceso
		$productosDelProceso = $this->getProductosDelProceso($entity->getId());
    	

    	$editForm = $this->createForm(new ProcesoType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    	
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_asignar_stock.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'procesoProducto'      => $productosDelProceso,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
    	));
    
    

    }
    
	
	public function asignarProductosAlStockUpdateAction($id) {
	
		$em = $this->getDoctrine()->getEntityManager();
	
		$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
	
		if (!$entity) {
			throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
		}

		//Traigo productos del proceso
		$productosDelProceso = $this->getProductosDelProceso($entity->getId());
	
		
		$request = $this->getRequest();
		foreach ($productosDelProceso as $pp):
	
		if ($request->request->has("cantidadIngresada_".$pp->getProducto()->getId())) {
			$pp->setCantidadIngresadaStock($request->get("cantidadIngresada_".$pp->getProducto()->getId()));
			//agrego al stock los productos
			$this->ingresarProducto($pp->getProducto(), $pp->getCantidadIngresadaStock());
	
		}
		else {
			$pp->setCantidadIngresadaStock(0);
		}
		
		$em->persist($pp);
		endforeach;
		
		$entity->setEstado(3);
		$entity->setFechaFin(new \DateTime);
		$em->persist($entity);
		
		$em->flush();
	
	
		$log = $this->get('log');
		$log->setPrioridad(1);
		$log->create($entity, "Se asignaron los productos al stock");
		
		$this->get('session')->setFlash('msj_info','Los productos se asignaron correctamente.');
		
		
		return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
	
	}

	
	
	//METODOS RELACIONADOS A LOS INSUMOS
	
	public function calcularDisponibilidadDeInsumos($insumo, $cantidad) {
		
		$em = $this->getDoctrine()->getEntityManager();
		
		$cantidadInsumoStock = $insumo->getCantidadEnStock();
						
		if (($cantidadInsumoStock - $cantidad ) < 0) return false;
		else return true;
		
	}
	
	public function getProductosDelProceso($id) {
		//Traigo el Proceso
		$em = $this->getDoctrine()->getEntityManager();
		$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
		
		if (!$entity) {
			throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
		}
		
		//Traigo productos del proceso
		$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
				WHERE prp.proceso = :po_id
				');
		$query->setParameters(array(
				'po_id' => $id,
		));
		$productosDelProceso = $query->getResult();
		
		return $productosDelProceso;
		
	}
	
	public function calcularUsoDeInsumos($id) {
		
		$productosDelProceso = $this->getProductosDelProceso($id);
		if (!$productosDelProceso) {
			throw $this->createNotFoundException('ERROR: El proceso no tiene productos.');
		}
		
		
		$insumosTotales = false;
		
		
		foreach ($productosDelProceso as $p):
			$insumosDelProducto = $p->getProducto()->getProductoInsumo();
			
			foreach ($insumosDelProducto as $ip):
				$idInsumo = $ip->getInsumo()->getId();
				if (!isset($insumosTotales[$idInsumo]["cantidad"]))
					$insumosTotales[$idInsumo]["cantidad"] = 0;
				$insumosTotales[$idInsumo]["cantidad"] += $p->getCantidad() * $ip->getCantidad();
				if (!isset($insumosTotales[$idInsumo]["cantidadProducida"]))
					$insumosTotales[$idInsumo]["cantidadProducida"] = 0;
				$insumosTotales[$idInsumo]["cantidadProducida"] += $p->getCantidadProducida() * $ip->getCantidad();
				$insumosTotales[$idInsumo]["nombre"] = $ip->getInsumo()->getNombre();
				$insumosTotales[$idInsumo]["unidad"] = $ip->getInsumo()->getUnidad();
				$insumosTotales[$idInsumo]["id"] = $idInsumo;
				$insumosTotales[$idInsumo]["disponible"] = $this->calcularDisponibilidadDeInsumos($ip->getInsumo(), $insumosTotales[$idInsumo]["cantidad"]);
			endforeach;
		endforeach;
		
		
		return $insumosTotales;
		
		
	
	}
	
	
	
	public function descontarInsumos($insumo, $cantidad) {
	
		$em = $this->getDoctrine()->getEntityManager();
	
		$entity  = new MovimientoStock();
		$entity->setElemento($insumo);
		$entity->setAccion(2);
		$entity->setMotivo(10);
		$entity->setCantidad($cantidad);
		$usuario = $this->container->get('security.context')->getToken()->getUser();
		$entity->setUsuario($usuario);
		$entity->setFechaHora(new \DateTime);
	
		$em->persist($entity);
		$em->flush();
	
		return true;
	
	}
	
	
	//METODOS RELACIONADOS A LOS PRODUCTOS
	
	public function ingresarProducto($producto, $cantidad) {
	
		$em = $this->getDoctrine()->getEntityManager();
	
		$entity  = new MovimientoStock();
		$entity->setElemento($producto);
		$entity->setAccion(1);
		$entity->setMotivo(1);
		$entity->setCantidad($cantidad);
		$usuario = $this->container->get('security.context')->getToken()->getUser();
		$entity->setUsuario($usuario);
		$entity->setFechaHora(new \DateTime);
	
		$em->persist($entity);
		$em->flush();
	
		return true;
	
	}
	
	public function agregarProdutosAlProceso($entity) {
		$request = $this->getRequest();
		$em = $this->getDoctrine()->getEntityManager();
		$numFilas = $request->get("numFilas");
		
		
		//Si ya existe el proceso (update) le borro todos los productos
		if ($entity->getId()) {
			$productos = $em->getRepository('DecoyesoProduccionBundle:ProcesoProducto')->findByProceso($entity->getId());

			foreach ($productos as $p):
				$em->remove($p);
			endforeach;
		}
				
	
		//Guardo la informacion del formulario en el array Items	
		for($i=0; $i<$numFilas; $i++):
			if (!$request->request->has("designacion_$i"))	continue;
			if (trim($request->get("cantidad_$i")) == "")	continue;
			
			$items[$request->get("designacion_$i")]['designacion'] = $request->get("designacion_$i");
			if (isset($items[$request->get("designacion_$i")]['cantidad']))
				$items[$request->get("designacion_$i")]['cantidad'] += $request->get("cantidad_$i");
			else
				$items[$request->get("designacion_$i")]['cantidad'] = $request->get("cantidad_$i");
			$items[$request->get("designacion_$i")]['cantidad_producida'] = 0;
		endfor;
	
	
		//Agrego los productos al proceso
		if (isset($items)):
			foreach ($items as $item):
				$producto = $em->getRepository('ProductoBundle:Producto')->findOneById($item['designacion']);
			
				if (!$producto) continue;
			
				$procesoProducto = new ProcesoProducto();
				$procesoProducto->setProceso($entity);
				$procesoProducto->setProducto($producto);
				$procesoProducto->setCantidad($item['cantidad']);
				$procesoProducto->setCantidadProducida($item['cantidad_producida']);
				$procesoProducto->setCantidadIngresadaStock(0);
				$em->persist($procesoProducto);

			endforeach;
		endif;
	
		$em->flush();
	
	}
	
    

	
	
	/* Esta no esta funcionando ahora */
	public function actualizarAction ($id)
	{
	
		$em = $this->getDoctrine()->getEntityManager();
	
		$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
	
		if (!$entity) {
			throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
		}
	
		/* Estimación de insumos a consumir */
		$insumos = $this->calcularUsoDeInsumos($entity->getId());
		$entity->setDatosInsumos(json_encode($insumos));
		$em->persist($entity);
		$em->flush();
	
		$log = $this->get('log');
		$log->create($entity, "Proceso actualizado");
	
		$this->get('session')->setFlash('msj_info','El Proceso se actualizó correctamente.');
	
		return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
	
	}
	
	
	
	
}
