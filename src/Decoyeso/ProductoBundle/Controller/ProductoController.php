<?php

namespace Decoyeso\ProductoBundle\Controller;

use Decoyeso\ProductoBundle\Entity\ProductoInsumo;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProductoBundle\Entity\Producto;
use Decoyeso\ProductoBundle\Form\ProductoType;

/**
 * Producto controller.
 *
 */
class ProductoController extends Controller
{
<<<<<<< HEAD
=======
	
	
	

	
>>>>>>> origin/desarrolloNico
    /**
     * Lists all Producto entities.
     *
     */
 public function indexAction($pararouting="index")
    {
        $buscador=$this->get("buscador");
    	$buscador->setRequest($this->getRequest());
    	$buscador->setPararouting($pararouting);
    
    	$buscador->setSql('SELECT p FROM ProductoBundle:Producto p');
    
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
    
    
    	return $this->render('ProductoBundle:Producto:admin_list.html.twig', array(
    			'entities' => $resultados["entities"],
    			'formBuscar'=>$resultados["form"]->createView(),
    	));
    }

    /**
     * Finds and displays a Producto entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Producto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductoBundle:Producto:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Producto entity.
     *
     */
    public function newAction()
    {
    	$em=$this->getDoctrine()->getEntityManager();
        $entity = new Producto();
        $form   = $this->createForm(new ProductoType(), $entity);

        $insumos=$em->getRepository('ProductoBundle:Insumo')->findAll();
        
        return $this->render('ProductoBundle:Producto:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'insumos'=>$insumos,
        ));
    }

    /**
     * Creates a new Producto entity.
     *
     */
    public function createAction()
    {
        $entity  = new Producto();
        $em = $this->getDoctrine()->getEntityManager();
        
        $request = $this->getRequest();
        $form    = $this->createForm(new ProductoType(), $entity);
        $form->bindRequest($request);
        
        $formProductos=$request->request->get('producto');
        
        if ($form->isValid()) {
        	
            
            $em->persist($entity);
            $em->flush();           

            
           $this->gestionarInsumos($formProductos['insumos'],$entity);
            
            
            $this->get('session')->setFlash('msj_info','El producto se ha creado correctamente.');
            
            return $this->redirect($this->generateUrl('producto_edit', array('id' => $entity->getId())));
            
        }
        
        $insumos=$em->getRepository('ProductoBundle:Insumo')->findAll();

        return $this->render('ProductoBundle:Producto:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'insumos'=>$insumos,
        ));
    }

    /**
     * Displays a form to edit an existing Producto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Producto')->find($id);
        
        
        $insumosIncluidos=$em->getRepository('ProductoBundle:ProductoInsumo')->findByProducto($id);
        
        $idInsumosIncluidos='0';
        foreach ($insumosIncluidos as $insumoc){
        	$idInsumosIncluidos.=','.$insumoc->getInsumo()->getId();
        }
        
        $insumosDisponibles=$em->createQuery('SELECT i FROM ProductoBundle:Insumo i WHERE i.id not in ('.$idInsumosIncluidos.')')->getResult();
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto entity.');
        }

        $editForm = $this->createForm(new ProductoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductoBundle:Producto:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        	'insumos'=>$insumosDisponibles,
        	'insumosIncluidos'=>$insumosIncluidos,
        ));
    }

    /**
     * Edits an existing Producto entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Producto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Producto entity.');
        }

        $editForm   = $this->createForm(new ProductoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);
        
        $formProductos=$request->request->get('producto');

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $this->gestionarInsumos($formProductos['insumos'],$entity);
            
            $this->get('session')->setFlash('msj_info','El producto se ha modificado correctamente.');

            return $this->redirect($this->generateUrl('producto_edit', array('id' => $id)));
        }
        
        $insumosIncluidos=$em->getRepository('ProductoBundle:ProductoInsumo')->findByProducto($id);
        
        $idInsumosIncluidos='0';
        foreach ($insumosIncluidos as $insumoc){
        	$idInsumosIncluidos.=','.$insumoc->getInsumo()->getId();
        }
        
        $insumosDisponibles=$em->createQuery('SELECT i FROM ProductoBundle:Insumo i WHERE i.id not in ('.$idInsumosIncluidos.')')->getResult();

        return $this->render('ProductoBundle:Producto:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        	'insumos'=>$insumosDisponibles,
        	'insumosIncluidos'=>$insumosIncluidos,        		
        ));
    }

    /**
     * Deletes a Producto entity.
     *
     */
    public function deleteAction($id)
    {
    	
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);
       
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
           
            $ProductoInsumos=$em->getRepository('ProductoBundle:ProductoInsumo')->findByProducto($id);
            foreach ($ProductoInsumos as $pi){
            	$em->remove($pi);
            	$em->flush();
            }
            
            
           
             
            $ServicioProductos=$em->getRepository('ProductoBundle:ServicioProducto')->findByProducto($id);
            foreach ($ServicioProductos as $sp){
            	$em->remove($sp);
            	$em->flush();
            }
            
            
          
            
            $entity = $em->getRepository('ProductoBundle:Producto')->find($id);

            
            
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Producto entity.');
            }

            $em->remove($entity);
            
            $em->flush();
        
        }

        return $this->redirect($this->generateUrl('producto'));
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
    			'msj'=>'¿Seguro desea eliminar el producto? ATENCIÓN !! El producto también será eliminado de los servicios con lo que este relacionado.'
    	));
    
    }
    
    public function gestionarInsumos($parametroInsumos,$producto){
    	
    	$em=$this->getDoctrine()->getEntityManager();
    	$insumos=explode(';',$parametroInsumos);

    	$objetosProductoInsumos=$em->getRepository('ProductoBundle:ProductoInsumo')->findByProducto($producto->getId());

    	foreach($objetosProductoInsumos as $objeto){
    		$em->remove($objeto);
    	}
    	$em->flush();

    	
    	for($i=1;$i<count($insumos);$i++){
    	
    		$productoInsumo[$i]=new ProductoInsumo();
    		$productoInsumo[$i]->setProducto($producto);
    		
    		$insumo=explode('@',$insumos[$i]);
    		
    		$objetoInsumo=$em->getRepository('ProductoBundle:Insumo')->find($insumo[0]);
    		$productoInsumo[$i]->setInsumo($objetoInsumo);
    		$productoInsumo[$i]->setCantidad($insumo[1]);
    		
    		$em->persist($productoInsumo[$i]);

    	}
    	
    	$em->flush();
    	    	
    }
    
}
