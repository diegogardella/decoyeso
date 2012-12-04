<?php

namespace Decoyeso\ClientesBundle\Controller;

use Coobix\AdminBundle\CoobixAdminBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ClientesBundle\Entity\Cliente;
use Decoyeso\ClientesBundle\Form\ClienteType;

/**
 * Cliente controller.
 *
 */
class ClienteController extends Controller
{
   
    public function indexAction($pararouting="index")
    {
    
    
    	$buscador=$this->get("buscador");
    	$buscador->setRequest($this->getRequest());
    	$buscador->setPararouting($pararouting);
    
    	$buscador->setSql('SELECT c FROM ClientesBundle:Cliente c ORDER BY c.id DESC');
    
    	$opciones=array(
    			"c_tipo"=>array('choice',array("label"=>"Tipo de cliente",'choices'=>	array(""=>"",1 => 'Persona Física', 2 => 'Organización'))),
    			"c_nombre"=>array(null,array("label"=>"Nombre")),
    			"c_cuitOcuil"=>array(null,array("label"=>"Cuit o Cuil ")),
    			"c_direccionBarrio"=>array(null,array("label"=>"Dirección - Barrio")),
    			"c_direccionCalle"=>array(null,array("label"=>"Dirección - Calle")),
    			"c_direccionNumero"=>array(null,array("label"=>"Dirección - Número")),
    			"c_numero"=>array(null,array("label"=>"Número")),
    			"c_email"=>array(null,array("label"=>"E-mail")),
    			"c_dni"=>array(null,array("label"=>"DNI"))
    			);
    	$buscador->setOpcionesForm($opciones);
    	

    
    	$resultados=$buscador->exeBuscar();
    
    
    	return $this->render('ClientesBundle:Cliente:admin_list.html.twig', array(
    			'entities' => $resultados["entities"],
    			'formBuscar'=>$resultados["form"]->createView(),
    	));
    
    
    }
   

    /**
     * Finds and displays a Cliente entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ClientesBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ClientesBundle:Cliente:admin_edit.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Cliente entity.
     *
     */
    public function newAction()
    {
    	$em=$this->getDoctrine()->getEntityManager();
    	
        $entity = new Cliente();
        
        $provincia=$em->getRepository('UbicacionBundle:Provincia')->find(11);
        $departamento=$em->getRepository('UbicacionBundle:Departamento')->find(345);
        $localidad=$em->getRepository('UbicacionBundle:Localidad')->find(3068);
		
        $entity->setProvincia($provincia);
        $entity->setDepartamento($departamento);
        $entity->setLocalidad($localidad);
        
        $form   = $this->createForm(new ClienteType(), $entity);

        return $this->render('ClientesBundle:Cliente:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Cliente entity.
     *
     */
    public function createAction()
    {
        $entity  = new Cliente();
        
        $em=$this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $f=$request->request->get('cliente');
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
        
        
        
        
        
        $form    = $this->createForm(new ClienteType(), $entity);
        
        if($bErrorUbicacion==''){
        
		        $form->bindRequest($request);
		
		        if ($form->isValid()) {
		            $em = $this->getDoctrine()->getEntityManager();
		            $em->persist($entity);
		            $em->flush();
		            
		            //LOG
		            $log = $this->get('log');
		            $log->create($entity, "Cliente Creado");
		
		            $this->get('session')->setFlash('msj_info','El cliente se ha creado correctamente');
		            
		            return $this->redirect($this->generateUrl('cliente_edit', array('id' => $entity->getId())));
		            
		        }
        }

        return $this->render('ClientesBundle:Cliente:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Cliente entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('ClientesBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }
        
        

        $editForm = $this->createForm(new ClienteType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ClientesBundle:Cliente:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Cliente entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $f=$request->request->get('cliente');

        $entity = $em->getRepository('ClientesBundle:Cliente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cliente entity.');
        }
        
        $provincia=$em->getRepository('UbicacionBundle:Provincia')->find($f['provincia']);
        $entity->setProvincia($provincia);
        
       	$departamento=$em->getRepository('UbicacionBundle:Departamento')->find($f['departamento']);
       	$entity->setDepartamento($departamento);
        

        $localidad=$em->getRepository('UbicacionBundle:Localidad')->find($f['localidad']);
        $entity->setLocalidad($localidad);
        

        $editForm   = $this->createForm(new ClienteType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        

        $editForm->bindRequest($request);

        
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Cliente Actualizado");

            $this->get('session')->setFlash('msj_info','El cliente se ha modificado correctamente');
            
            return $this->redirect($this->generateUrl('cliente_edit', array('id' => $id)));
        }

        return $this->render('ClientesBundle:Cliente:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    



    /**
     * Deletes a Cliente entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);
        
        if ($form->isValid()) {
        	
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('ClientesBundle:Cliente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cliente entity.');
            }

            //LOG
            $log = $this->get('log');
            $log->create($entity, "Cliente Eliminado");
            
            $em->remove($entity);
            $em->flush();
            

        }

        return $this->redirect($this->generateUrl('cliente'));
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
    			'url' => $this->generateUrl('cliente_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el cliente? ¡¡ADVERTENCIA!! Al eliminar un cliente también se eliminaran los pedidos, relevamientos y presupuestos correspondientes al mismo.'
    	));
    
    }
    
}
