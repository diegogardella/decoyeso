<?php

namespace Decoyeso\ProductoBundle\Controller;

use Decoyeso\ProductoBundle\Entity\ServicioProducto;

use Decoyeso\ProductoBundle\Entity\ServicioInsumo;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProductoBundle\Entity\Servicio;
use Decoyeso\ProductoBundle\Form\ServicioType;

/**
 * Servicio controller.
 *
 */
class ServicioController extends Controller
{
    /**
     * Lists all Servicio entities.
     *
     */
 public function indexAction($pararouting="index")
    {
        $buscador=$this->get("buscador");
    	$buscador->setRequest($this->getRequest());
    	$buscador->setPararouting($pararouting);
    
    	$buscador->setSql('SELECT s FROM ProductoBundle:Servicio s');
    
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
    
    
    	return $this->render('ProductoBundle:Servicio:admin_list.html.twig', array(
    			'entities' => $resultados["entities"],
    			'formBuscar'=>$resultados["form"]->createView(),
    	));
    }

    /**
     * Finds and displays a Servicio entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Servicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Servicio entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductoBundle:Servicio:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Servicio entity.
     *
     */
    public function newAction()
    {
    	$em=$this->getDoctrine()->getEntityManager();
        $entity = new Servicio();
        $form   = $this->createForm(new ServicioType(), $entity);

        $insumos=$em->getRepository('ProductoBundle:Insumo')->findAll();
        $productos=$em->getRepository('ProductoBundle:Producto')->findAll();
        
        
        return $this->render('ProductoBundle:Servicio:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'insumos'=>$insumos,
        	'productos'=>$productos,
        ));
    }

    /**
     * Creates a new Servicio entity.
     *
     */
    public function createAction()
    {
        $entity  = new Servicio();
        
        
        $request = $this->getRequest();
        $form    = $this->createForm(new ServicioType(), $entity);
        $form->bindRequest($request);
        
        $formProductos=$request->request->get('servicio');
        
        if ($form->isValid()) {
        	
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();           

            
           $this->gestionarInsumos($formProductos['insumos'],$formProductos['productos'],$entity);
            
            
            $this->get('session')->setFlash('msj_info','El servicio se ha creado correctamente.');
            
            return $this->redirect($this->generateUrl('servicio_edit', array('id' => $entity->getId())));
            
        }

        return $this->render('ProductoBundle:Servicio:admin_new.html.twig', array(
            'entity' => $entity,
           'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Servicio entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Servicio')->find($id);
        
        
        $insumosIncluidos=$em->getRepository('ProductoBundle:ServicioInsumo')->findByServicio($id);
        
        $idInsumosIncluidos='0';
        foreach ($insumosIncluidos as $insumoc){
        	$idInsumosIncluidos.=','.$insumoc->getInsumo()->getId();
        }
        
        $insumosDisponibles=$em->createQuery('SELECT i FROM ProductoBundle:Insumo i WHERE i.id not in ('.$idInsumosIncluidos.')')->getResult();
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Servicio entity.');
        }
        
        
        
        $productosIncluidos=$em->getRepository('ProductoBundle:ServicioProducto')->findByServicio($id);
        
        $idProductosIncluidos='0';
        foreach ($productosIncluidos as $productoc){
        	$idProductosIncluidos.=','.$productoc->getProducto()->getId();
        }
        
        $productosDisponibles=$em->createQuery('SELECT p FROM ProductoBundle:Producto p WHERE p.id not in ('.$idProductosIncluidos.')')->getResult();
        
        
        if (!$entity) {
        	throw $this->createNotFoundException('Unable to find Servicio entity.');
        }
        
        
        

        $editForm = $this->createForm(new ServicioType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductoBundle:Servicio:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        	'insumos'=>$insumosDisponibles,
        	'insumosIncluidos'=>$insumosIncluidos,
        	'productos'=>$productosDisponibles,
        	'productosIncluidos'=>$productosIncluidos,
        ));
    }

    /**
     * Edits an existing Servicio entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Servicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Servicio entity.');
        }

        $editForm   = $this->createForm(new ServicioType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);
        
        $formProductos=$request->request->get('servicio');

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $this->gestionarInsumos($formProductos['insumos'],$formProductos['productos'],$entity);
            
            $this->get('session')->setFlash('msj_info','El servicio se ha modificado correctamente.');

            return $this->redirect($this->generateUrl('servicio_edit', array('id' => $id)));
        }

        return $this->render('ProductoBundle:Servicio:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Servicio entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ProductoBundle:Servicio')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Servicio entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('servicio'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function listDeleteformAction($id,$extra="")
    {
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:list_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('producto_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el servicio? '
    	));
    
    }
    
    public function gestionarInsumos($parametroInsumos,$parametroProductos,$servicio){
    	
    	$em=$this->getDoctrine()->getEntityManager();
    	$insumos=explode(';',$parametroInsumos);
    	$productos=explode(';',$parametroProductos);

    	$objetosServicioInsumos=$em->getRepository('ProductoBundle:ServicioInsumo')->findByServicio($servicio->getId());

    	foreach($objetosServicioInsumos as $objeto){
    		$em->remove($objeto);
    	}
    	$em->flush();

    	$objetosServicioProductos=$em->getRepository('ProductoBundle:ServicioProducto')->findByServicio($servicio->getId());
    	
    	foreach($objetosServicioProductos as $objeto){
    		$em->remove($objeto);
    	}
    	$em->flush();
    	
    	
    	for($i=1;$i<count($insumos);$i++){
    	
    		$servicioInsumo[$i]=new ServicioInsumo();
    		$servicioInsumo[$i]->setServicio($servicio);
    		
    		$insumo=explode('@',$insumos[$i]);
    		
    		$objetoInsumo=$em->getRepository('ProductoBundle:Insumo')->find($insumo[0]);
    		$servicioInsumo[$i]->setInsumo($objetoInsumo);
    		$servicioInsumo[$i]->setCantidad($insumo[1]);
    		
    		$em->persist($servicioInsumo[$i]);

    	}
    	
    	
    	for($j=1;$j<count($productos);$j++){
    	
    		$servicioProducto[$j]=new ServicioProducto();
    		$servicioProducto[$j]->setServicio($servicio);
    	
    		$producto=explode('@',$productos[$j]);
    	
    		$objetoProducto=$em->getRepository('ProductoBundle:Producto')->find($producto[0]);
    		$servicioProducto[$j]->setProducto($objetoProducto);
    		$servicioProducto[$j]->setCantidad($producto[1]);
    	
    		$em->persist($servicioProducto[$j]);
    	
    	}
    	
    	$em->flush();
    	    	
    }
    
}
