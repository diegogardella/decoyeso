<?php

namespace Decoyeso\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\UsuarioBundle\Entity\Usuario;
use Decoyeso\UsuarioBundle\Form\UsuarioType;
use Decoyeso\UsuarioBundle\Form\ProfileUsuarioType;
use Decoyeso\UsuarioBundle\Form\RegistrationUsuarioType;
use Decoyeso\UsuarioBundle\Form\RegistrationUsuarioNewType;

/**
 * Usuario controller.
 *
 */
class UsuarioController extends Controller
{
    /**
     * Lists all Usuario entities.
     *
     */
	public function indexAction($pararouting="index")
	{
		$buscador=$this->get("buscador");
		$buscador->setRequest($this->getRequest());
		$buscador->setPararouting($pararouting);
	
		$buscador->setSql('SELECT u FROM UsuarioBundle:Usuario u WHERE u.enabled=1 order by u.apellido asc');
	
	
		$resultados=$buscador->exeBuscar();
	
	
		return $this->render('UsuarioBundle:Usuario:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
		));
	
	
	}
    
    

    public function editAction($id)
    {
    
    	$userManager = $this->get('fos_user.user_manager');
    	
    	$entity = $userManager->findUserBy(array("id"=>$id));
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$editForm = $this->createForm(new ProfileUsuarioType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    

    	
    	return $this->render('UsuarioBundle:Usuario:admin_edit.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView(),
    	));
    }
    

    public function updateAction($id)
    {
    	
   		$userManager = $this->get('fos_user.user_manager');
    	
    	$entity = $userManager->findUserBy(array("id"=>$id));
    	
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$editForm   = $this->createForm(new ProfileUsuarioType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    
    	$request = $this->getRequest();

    	$editForm->bindRequest($request);
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	
        $err = false;
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery(" SELECT u FROM UsuarioBundle:Usuario u
    			WHERE u.email = :u_email AND u.id != :u_id
    			");
    	$query->setParameters(array(
    			'u_email' => $entity->getEmail(),
    			'u_id' => $entity->getId(),
    	));
    	$auxUser = $query->getResult();
    	
    	
    	if ($auxUser) {
    		$errorMsj[]= "El Email ya existe.";
    		$err = true;
    	}
    	
    
    	if ($editForm->isValid() && !$err) {
    		$userManager->updateUser($entity);
    		
			$this->get('session')->setFlash('msj_info','El usuario se ha modificado correctamente');
    		//LOG
    		$log = $this->get('log');
    		$log->create($entity, "Usuario Actualizado");
    		
    		return $this->redirect($this->generateUrl('usuario_edit', array('id' => $entity->getId())));
    		
    	}
    
    	
    	return $this->render('UsuarioBundle:Usuario:admin_edit.html.twig', array(
    			'errorMsj' => $errorMsj,
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    			'delete_form' => $deleteForm->createView(),
    	));
    }
    
    /**
     * Deletes a Usuario entity.
     *
     */
    public function deleteAction($id)
    {
    	$form = $this->createDeleteForm($id);
    	$request = $this->getRequest();
    
    	$form->bindRequest($request);
    
    	if ($form->isValid()) {
    		$em = $this->getDoctrine()->getEntityManager();
    		$entity = $em->getRepository('UsuarioBundle:Usuario')->find($id);
    
    		if (!$entity) {
    			throw $this->createNotFoundException('Unable to find Usuario entity.');
    		}
    		
    		//LOG
    		$log = $this->get('log');
    		$log->create($entity, "Usuario Eliminado");
   			
    		$userManager = $this->get('fos_user.user_manager');
    		$entity = $userManager->findUserBy(array("id"=>$id));
    		$entity->setEnabled(false);
    		$userManager->updateUser($entity);
    		
			
    	}
    
    	return $this->redirect($this->generateUrl('usuario'));
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
    			'url' => $this->generateUrl('usuario_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el usuario?'
    	));
    
    }

    /**
     * Displays a form to create a new Usuario entity.
     *
     */
    public function newAction()
    {
        
        $userManager = $this->get('fos_user.user_manager');
        $entity = $userManager->createUser();
        
        $form   = $this->createForm(new RegistrationUsuarioNewType(), $entity);

        return $this->render('UsuarioBundle:Usuario:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
	
    
    /**
     * Creates a new Usuario entity.
     *
     */
    public function createAction()
    {
    	
        $userManager = $this->get('fos_user.user_manager');
        $entity = $userManager->createUser();
        $id = $entity->getId();
        $request = $this->getRequest();
        $form    = $this->createForm(new RegistrationUsuarioNewType(), $entity);
        $form->bindRequest($request);

        
        $err = false;
        $errorMsj = array();
        if ( $entity->getNombre() == "") {
        	$errorMsj[]= "Debe ingresar un nombre.";
        	$err = true;
        }
        if ( $entity->getEmail() == "") {
        	$errorMsj[]= "Debe ingresar un Email.";
        	$err = true;
        }
        if ( $entity->getUsername() == "") {
        	$errorMsj[]= "Debe ingresar un Nombre de Usuario.";
        	$err = true;
        }
        if ( $entity->getPlainPassword() == "") {
        	$errorMsj[]= "Debe ingresar una Contraseña.";
        	$err = true;
        }
        
		if (!$err) {	        
	        //Me fijo si ya existe el usuario
	        $em = $this->getDoctrine()->getEntityManager();
	                
	        $auxUser = $em->getRepository('UsuarioBundle:Usuario')->findByEmail($entity->getEmail());
	        if ($auxUser) {
	        	$errorMsj[]= "El Email ya existe.";
	        	$err = true;
	        } 
	        $auxUser = $em->getRepository('UsuarioBundle:Usuario')->findByUsername($entity->getUsername());
			if ($auxUser) {
	        	$errorMsj[]= "El Usuario ya existe.";
	        	$err = true;
	        }
		}
          
      
               
        if ($form->isValid() && !$err) {
        	$entity->setEnabled(true);
        	$entity->addRole("ROLE_SUPER_ADMIN");
        	
            $userManager->updateUser($entity);
            
	    	$this->get('session')->setFlash('msj_info','El usuario se ha creado correctamente');

            //LOG
            $log = $this->get('log');
            $log->create($entity, "Usuario Creado");
            
            $editForm = $this->createForm(new ProfileUsuarioType(), $entity);
            $deleteForm = $this->createDeleteForm($id);
            return $this->redirect($this->generateUrl('usuario_edit', array(
            		'id' => $entity->getId(),
            		'edit_form'   => $editForm->createView(),
            		'delete_form' => $deleteForm->createView())));
            
        }

        return $this->render('UsuarioBundle:Usuario:admin_new.html.twig', array(
        	'errorMsj' => $errorMsj,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
	
    
   
    

}
