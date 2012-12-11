<?php

namespace Decoyeso\PedidoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\PedidoBundle\Entity\Pedido;
use Decoyeso\PedidoBundle\Form\PedidoType;

/**
 * Pedido controller.
 *
 */
class PedidoController extends Controller
{
    /**
     * Lists all Pedido entities.
     *
     */
public function indexAction($pararouting="index")
	{
		$buscador=$this->get("buscador");
		$buscador->setRequest($this->getRequest());
		$buscador->setPararouting($pararouting);
	
		$buscador->setSql('SELECT p FROM PedidoBundle:Pedido p join p.cliente c order by p.estado asc, p.fechaEntrega asc, p.prioridad desc');
	
		$opciones=array(
				"p_numero"=>array(null,array("label"=>"Número de pedido")),
    			"p_fechaEntrega"=>array("date", array("empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"),"format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Entregar el')),
    			"p_fechaCreado"=>array("date",array("empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"),"format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Creado el')),
    			"p_fechaSolicitado"=>array("date", array("empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"),"format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Solicitado el')),
				"p_obra"=>array(null,array("label"=>"Obra")),
    			"p_descripcion"=>array(null,array("label"=>"Descripción")),
				"p_requiereRelevamiento"=>array('choice',array('choices' =>array(""=>"",0=>"No",1=>"Si"),"label"=>"Requiere relevamiento")),
				"p_prioridad"=>array('choice',array('choices' =>array(""=>"",1=>"Baja",2=>"Media",3=>"Alta",4=>"Muy alta"),"label"=>"Prioridad")),
				"p_estado"=>array('choice',array('choices' =>array(""=>"",1=>"En espera",2=>"Relevamiento creado",3=>"Presupuesto creado"),"label"=>"Estado")),
    			"c_numero"=>array(null,array("label"=>"Número de cliente")),
    			"c_nombre"=>array(null,array("label"=>"Cliente")),
    			"c_dni"=>array(null,array("label"=>"DNI de Cliente")),
				"c_cuitOcuil"=>array(null,array("label"=>"Cuit o Cuil de Cliente "))
    			);
		
		$buscador->setOpcionesForm($opciones);
	
		$resultados=$buscador->exeBuscar();
	
	
		return $this->render('PedidoBundle:Pedido:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
		));
	
	
	}

	
	public function cambiarEstadoAction($id,$estado){
	
		$em = $this->getDoctrine()->getEntityManager();	
		$pedido = $em->getRepository('PedidoBundle:Pedido')->find($id);
		$pedido->setEstado($estado);
		$em->persist($pedido);
		$em->flush();

		return $this->redirect($this->generateUrl('pedido_edit',array('id'=>$pedido->getId())));
	}
	
	
	public function pedidoPorClienteAction($cliente){
		
		$em = $this->getDoctrine()->getEntityManager();
		
		$clienteDelPedido = $em->getRepository('ClientesBundle:Cliente')->find($cliente);		
		
		$entity = $em->getRepository('PedidoBundle:Pedido')->findBy(array('cliente'=>$cliente),array('estado'=>'ASC','fechaEntrega'=>'ASC','prioridad'=>'DESC'));
		
		return $this->render('PedidoBundle:Pedido:admin_list_por_cliente.html.twig', array(
				'entities' => $entity,
				'cliente'=>$clienteDelPedido
		));
		
	}
	
    /**
     * Finds and displays a Pedido entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('PedidoBundle:Pedido')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('ERROR: no se encontró el pedido.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PedidoBundle:Pedido:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Pedido entity.
     *
     */
    public function newAction($cliente=0)
    {
    	$em=$this->getDoctrine()->getEntityManager();
    	
        $entity = new Pedido();
        $provincia=$em->getRepository('UbicacionBundle:Provincia')->find(11);
        $departamento=$em->getRepository('UbicacionBundle:Departamento')->find(345);
        $localidad=$em->getRepository('UbicacionBundle:Localidad')->find(3068);
        
        $entity->setProvincia($provincia);
        $entity->setDepartamento($departamento);
        $entity->setLocalidad($localidad);
        
        $entity->setFechaSolicitado(new \DateTime('today'));
        $entity->setFechaEntrega(new \DateTime('tomorrow'));
        $entity->setPrioridad(2);
        
        if($cliente!=0){
	        $clienteEntity=$em->getRepository('ClientesBundle:Cliente')->find($cliente);
	       	$entity->setCliente($clienteEntity);
        }
        
        $form   = $this->createForm(new PedidoType(), $entity);

        return $this->render('PedidoBundle:Pedido:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Pedido entity.
     *
     */
    public function createAction()
    {
        $entity  = new Pedido();
        
        $em=$this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $f=$request->request->get('pedido');
        $bErrorUbicacion='';
        
        $provincia=$em->getRepository('UbicacionBundle:Provincia')->find($f['provincia']);
        $entity->setProvincia($provincia);
        

        if($f['departamento']!=''){
        	$departamento=$em->getRepository('UbicacionBundle:Departamento')->find($f['departamento']);
        	$entity->setDepartamento($departamento);
        
        }else{
        	$bErrorUbicacion="Seleccione Departamento";
        	$departamento=$em->getRepository('UbicacionBundle:Departamento')->findOneByProvincia($provincia->getId());
        	$entity->setDepartamento($departamento);
        }
        

        if($f['localidad']!=''){	
        	$localidad=$em->getRepository('UbicacionBundle:Localidad')->find($f['localidad']);
        	$entity->setLocalidad($localidad);
        	

        }else{
        	$bErrorUbicacion="Seleccione Localidad";
        	$localidad=$em->getRepository('UbicacionBundle:Localidad')->findOneByDepartamento($departamento->getId());
        	$entity->setLocalidad($localidad);
        }
        
      
        
        $form = $this->createForm(new PedidoType(), $entity);
        
       
        
        if($bErrorUbicacion==''){
        
        		$form->bindRequest($request);
        		
		        if ($form->isValid()) {
		            
		            $em->persist($entity);    
		            
		            $em->flush();
		            
		            $this->get('session')->setFlash('msj_info','El pedido se ha creado correctamente');
		            
		            //LOG
		            $log = $this->get('log');
		            $log->create($entity, "Pedido creado");
		
		            return $this->redirect($this->generateUrl('pedido_edit',array('id'=>$entity->getId())));
		            
		        }
        }

        return $this->render('PedidoBundle:Pedido:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Pedido entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('PedidoBundle:Pedido')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('ERROR: no se encontró el pedido.');
        }

        $editForm = $this->createForm(new PedidoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('PedidoBundle:Pedido:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Edits an existing Pedido entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $f=$request->request->get('pedido');
        $entity = $em->getRepository('PedidoBundle:Pedido')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('ERROR: no se encontró el pedido.');
        }
        
        $provincia=$em->getRepository('UbicacionBundle:Provincia')->find($f['provincia']);
        $entity->setProvincia($provincia);
        
        $departamento=$em->getRepository('UbicacionBundle:Departamento')->find($f['departamento']);
        $entity->setDepartamento($departamento);
        
        
        $localidad=$em->getRepository('UbicacionBundle:Localidad')->find($f['localidad']);
        $entity->setLocalidad($localidad);

        $editForm   = $this->createForm(new PedidoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $entity->verificarEstado();
            $em->flush();
            
            
            $this->get('session')->setFlash('msj_info','El pedido se ha modificado correctamente');
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Pedido actualizado");

            return $this->redirect($this->generateUrl('pedido_edit', array('id' => $id)));
        }

        return $this->render('PedidoBundle:Pedido:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Pedido entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);
        
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('PedidoBundle:Pedido')->find($id);
        

        if ($form->isValid() ) {
           

            if (!$entity) {
               throw $this->createNotFoundException('ERROR: no se encontró el pedido.');
            }
            
            try {
	            $em->remove($entity);
	            $log = $this->get('log');
	            $log->create($entity, "Pedido eliminado");
	            $em->flush();
            }
            catch (\Exception $e) {
            	$this->get('session')->setFlash('msj_info','El pedido no se pudo eliminar. Debido a que tiene datos asociados.');
            	return $this->redirect($this->generateUrl('pedido_edit', array('id' => $id)));
            }

        }

        return $this->redirect($this->generateUrl('pedido'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    
    public function accionDeleteformAction($id,$extra="")
    {
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:accion_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('pedido_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el pedido?'
    	));
    
    }
    
    public function listDeleteformAction($id)
    {
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:list_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('pedido_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el pedido?'
    	));
    
    }
    
   
    
}
