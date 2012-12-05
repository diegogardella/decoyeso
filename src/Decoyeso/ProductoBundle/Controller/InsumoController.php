<?php

namespace Decoyeso\ProductoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProductoBundle\Entity\Insumo;
use Decoyeso\ProductoBundle\Form\InsumoType;

/**
 * Insumo controller.
 *
 */
class InsumoController extends Controller
{
    /**
     * Lists all Insumo entities.
     *
     */
public function indexAction($pararouting="index")
    {
        $buscador=$this->get("buscador");
    	$buscador->setRequest($this->getRequest());
    	$buscador->setPararouting($pararouting);
    
    	$buscador->setSql('SELECT i FROM ProductoBundle:Insumo i');
    
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
    
    
    	return $this->render('ProductoBundle:Insumo:admin_list.html.twig', array(
    			'entities' => $resultados["entities"],
    			'formBuscar'=>$resultados["form"]->createView(),
    	));
    }

    /**
     * Finds and displays a Insumo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Insumo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Insumo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductoBundle:Insumo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Insumo entity.
     *
     */
    public function newAction()
    {
        $entity = new Insumo();
        $form   = $this->createForm(new InsumoType(), $entity);

        return $this->render('ProductoBundle:Insumo:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Insumo entity.
     *
     */
    public function createAction()
    {
        $entity  = new Insumo();
        $request = $this->getRequest();
        $form    = $this->createForm(new InsumoType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('msj_info','El insumo se ha creado correctamente.');
            
            return $this->redirect($this->generateUrl('insumo_edit', array('id' => $entity->getId())));
            
        }

        return $this->render('ProductoBundle:Insumo:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Insumo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Insumo')->find($id);
        
        $insumosStock=$em->getRepository('StockBundle:MovimientoStock')->findByElemento($id);
        $cantidadInsumoStock=0;
        foreach($insumosStock as $insumoStock){
        	if($insumoStock->getAccion()==1){
        		$cantidadInsumoStock=$cantidadInsumoStock+$insumoStock->getCantidad();
        	}else{
        		$cantidadInsumoStock=$cantidadInsumoStock-$insumoStock->getCantidad();
        	}
        	 
        }
        
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Insumo entity.');
        }

        
        $editForm = $this->createForm(new InsumoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        
        
        return $this->render('ProductoBundle:Insumo:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        	'cantidadInsumoStock'=>$cantidadInsumoStock        		
        ));
    }

    /**
     * Edits an existing Insumo entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ProductoBundle:Insumo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Insumo entity.');
        }

        $editForm   = $this->createForm(new InsumoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->setFlash('msj_info','El insumo se ha modificado correctamente.');
            return $this->redirect($this->generateUrl('insumo_edit', array('id' => $id)));
        }

        return $this->render('ProductoBundle:Insumo:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Insumo entity.
     *
     */
    public function deleteAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        $productoInsumos=$em->getRepository('ProductoBundle:ProductoInsumo')->findByInsumo($id);
        $servicioInsumos=$em->getRepository('ProductoBundle:ServicioInsumo')->findByInsumo($id);
        $insumoPresupuesto=$em->getRepository('PedidoBundle:PresupuestoElemento')->findByElemento($id);
        
        if ($form->isValid() and count($productoInsumos)==0 and count($servicioInsumos)==0 and count($insumoPresupuesto)==0){
        	
        	
        	
            
            $entity = $em->getRepository('ProductoBundle:Insumo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Insumo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('insumo'));
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
    			'url' => $this->generateUrl('insumo_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el insumo?'
    	));
    
    }
    
    public function listDeleteformAction($id,$extra="")
    {
    	$deleteForm = $this->createDeleteForm($id);
    	    
    	return $this->render('CoobixAdminBundle:Default:list_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('insumo_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el insumo?'
    	));
    
    }
    
    
}
