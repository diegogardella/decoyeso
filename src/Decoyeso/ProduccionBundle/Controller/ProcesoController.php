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
	
			$em->persist($entity);
			$em->flush();
			$this->agregarProdutosAlProceso($entity);
	
			/* Estimación de insumos a consumir*/
			$insumos = $this->calcularUsoDeInsumos($entity->getId());
			$entity->setDatosInsumos(json_encode($insumos));
			$em->persist($entity);
			$em->flush();
	
	
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
    			'po_id' => $entity->getId(),
    	));
    	$procesoProducto = $query->getResult();
    	
    	
    	//$this->asignarProductosSecadores($entity->getId());
    	
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_show.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'procesoProducto'      => $procesoProducto,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
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
    
    		/* Estimación de insumos a consumir*/
    		$insumos = $this->calcularUsoDeInsumos($entity->getId());
    		$entity->setDatosInsumos(json_encode($insumos));
    		$em->persist($entity);
    		$em->flush();
    
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
    
    
    public function iniciarAction ($id) {
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Proceso entity.');
    	}
    	
    	
    
    	
    	$entity->setEstado(1);
    	$entity->setFechaInicio(new \DateTime);
    	$em->flush($entity);
    	
    	$log = $this->get('log');
    	$log->create($entity, "Proceso Iniciado");
    	
    	//Me fijo si se inicio el proceso sin insumos
    	$datosInsumos = $entity->getDatosInsumos();
    	$insumos = "";
    	foreach($datosInsumos as $d):
	    	if(!$d["disponible"]) {
	    		$insumos .= " ".$d["nombre"].",";
	    	}
	    endforeach;
	    
    	if ($insumos!="") {
    		$log = $this->get('log');
    		$log->setPrioridad(1);
    		$log->setPermisos("ROLE_DEPOSITO");
    		$log->create(false, "El Proceso ".$entity." se ha Iniciado sin stock necesario de".substr($insumos, 0, -1));
    	}

    	
    	
    	
    	
    	$this->get('session')->setFlash('msj_info','El Proceso se ha Iniciado correctamente');
    
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
    	foreach ($datosInsumos as $di) {
    		$insumo = $em->getRepository('ProductoBundle:Insumo')->find($di["id"]);
    		$this->descontarInsumos($insumo, $di["cantidadProducida"]);
    	}
    	
    	$log = $this->get('log');
    	$log->create($entity, "Proceso Finalizado");
    	
    	$this->get('session')->setFlash('msj_info','El Proceso se ha Finalizado correctamente');
    
    
    	return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
    
    
    }
    
    
    
    public function asignarProductosAlStockAction($id) {
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entity = $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find Proceso entity.');
    	}
    	
    	
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
    			'po_id' => $entity->getId(),
    	));
    	$procesoProducto = $query->getResult();
    	
    	
    	    	$editForm = $this->createForm(new ProcesoType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    	
    	return $this->render('DecoyesoProduccionBundle:Proceso:admin_asignar_stock.html.twig', array(
    			'entity'      => $entity,
    			'productos'      => $productos,
    			'procesoProducto'      => $procesoProducto,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView()
    	));
    
    

    }
    
    

    
    


	

	
	public function asignarProductosAlStockUpdateAction($id) {
	
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
		$log->create($entity, "Se asignaron los productos al stock");
		
		$this->get('session')->setFlash('msj_info','Los productos se asignaron correctamente');
		
		
		return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
	
	}
	
	//METODOS RELACIONADOS CON LOS SECADORES
	
	public function asignarProductosSecadores ($id) {
	
		$em = $this->getDoctrine()->getEntityManager();
	
		$proceso= $em->getRepository('DecoyesoProduccionBundle:Proceso')->find($id);
	
		if (!$proceso) {
			throw $this->createNotFoundException('Unable to find Proceso entity.');
		}
	
		$request = $this->getRequest();
	
		//Traigo productos del proceso
		$query = $em->createQuery('SELECT prp FROM DecoyesoProduccionBundle:ProcesoProducto prp
				WHERE prp.proceso = :po_id
				');
		$query->setParameters(array(
				'po_id' => $proceso->getId(),
		));
		$productos = $query->getResult();
	
		if (!$productos) {
			throw $this->createNotFoundException('El proceso no tiene productos.');
		}
	
		//Recorro los productos para juntarlos por placas o molduras
		$placas = array();
		$molduras = array();
		foreach ($productos as $pp):
	
		//Placas
		if ($pp->getProducto()->getTipo() == 0) {
			for ($i=0; $i<$pp->getCantidad(); $i++) {
				$placas[] = $pp->getProducto();
			}
	
		}
		//Molduras
		if ($pp->getProducto()->getTipo() == 1) {
			for ($i=0; $i<$pp->getCantidad(); $i++) {
				$molduras[] = $pp->getProducto();
			}
		}
	
		endforeach;
	
	
	
		//Si hay placas para producir
		if (count($placas)>0) {
	
			//Busco Secadores de placas
			$secadores = $em->getRepository('DecoyesoProduccionBundle:Secador')->findByTipo(0);
	
			if (!$secadores) {
				throw $this->createNotFoundException('No hay secadores.');
			}
	
			if ($secadores) {
				//Elijo el secador que conviene
				//....
				$secadoresParaElProceso = $secadores;
	
	
				$cantidadPlacas = count($placas);
				foreach ($secadoresParaElProceso as $sP):
				$lugaresSecador = $sP->getLugaresSecador();
				foreach ($lugaresSecador as $lP):
				if ($cantidadPlacas < 1) break;
				if (!$lP->getDisponible()) {
					$cantidadPlacas--;
					$lP->setDisponible(1);
					$lP->setProceso($proceso);
					$lP->setFechaAsignado (new \DateTime);
	
					echo $lP->calularDias();
					exit();
					$em->persist($lP);
				}
				endforeach;
				endforeach;
			}
	
			$em->flush();
	
	
	
		}
	
		//Si hay placas para producir
		if (count($molduras)>0) {
	
			//Busco Secadores de placas
			$secadores = $em->getRepository('DecoyesoProduccionBundle:Secador')->findByTipo(1);
	
			if (!$secadores) {
				throw $this->createNotFoundException('No hay secadores.');
			}
	
			if ($secadores) {
				//Elijo el secador que conviene
				//....
				$secadoresParaElProceso = $secadores;
	
	
				$cantidadPlacas = count($placas);
				foreach ($secadoresParaElProceso as $sP):
				$lugaresSecador = $sP->getLugaresSecador();
				foreach ($lugaresSecador as $lP):
				if ($cantidadPlacas < 1) break;
				if (!$lP->getDisponible()) {
					$cantidadPlacas--;
					$lP->setDisponible(1);
					$lP->setProceso($proceso);
					$lP->setFechaAsignado (new \DateTime);
					$em->persist($lP);
				}
				endforeach;
				endforeach;
			}
	
			$em->flush();
	
	
	
		}
	
		//return $this->redirect($this->generateUrl('proceso_show', array('id' => $id)));
	}
	
	
	//METODOS RELACIONADOS A LOS INSUMOS
	
	public function calcularDisponibilidadDeInsumos($insumo, $cantidad) {
		
		$em = $this->getDoctrine()->getEntityManager();
		
		$insumosStock=$em->getRepository('StockBundle:MovimientoStock')->findByElemento($insumo->getId());
		$cantidadInsumoStock=0;
		foreach($insumosStock as $insumoStock){
			if($insumoStock->getAccion()==1){
				$cantidadInsumoStock=$cantidadInsumoStock+$insumoStock->getCantidad();
			}else{
				$cantidadInsumoStock=$cantidadInsumoStock-$insumoStock->getCantidad();
			}
		}
		
		if (($cantidadInsumoStock - $cantidad ) < 0) return false;
		else return true;
		
	}
	
	public function calcularUsoDeInsumos($id) {
		
		//Traigo el Proceso
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
		$productosDelProceso = $query->getResult();
		
		$insumosTotales = false;
		
		if ($productosDelProceso) {
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
		}
		
		return $insumosTotales;
		
	
	}
	
	
	
	public function descontarInsumos($insumo, $cantidad) {
	
		$em = $this->getDoctrine()->getEntityManager();
	
		$entity  = new MovimientoStock();
		$entity->setElemento($insumo);
		$entity->setAccion(2);
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
	
		$productos = $em->getRepository('DecoyesoProduccionBundle:ProcesoProducto')->findByProceso($entity->getId());
		//echo count ($productos);
		foreach ($productos as $p):
		$em->remove($p);
		endforeach;
	
		$em->flush();
	
	
	
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
		$procesoProducto->setCantidadIngresadaStock(0);
		$em->persist($procesoProducto);
	
	
		endforeach;
		endif;
	
		$em->flush();
	
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
}
