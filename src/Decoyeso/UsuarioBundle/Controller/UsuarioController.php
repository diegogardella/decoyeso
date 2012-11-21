<?php

namespace Decoyeso\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\UsuarioBundle\Entity\Usuario;
use Decoyeso\UsuarioBundle\Form\UsuarioType;
use Decoyeso\UsuarioBundle\Form\ProfileUsuarioType;
use Decoyeso\UsuarioBundle\Form\RegistrationUsuarioType;
use Decoyeso\UsuarioBundle\Form\RegistrationUsuarioNewType;
use Decoyeso\UsuarioBundle\Form\ChangePassUsuarioType;
use Decoyeso\UsuarioBundle\Form\CambiarPermisosUsuarioType;


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
		if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
			throw $this->createNotFoundException('ERROR: No tiene permisos.');
		endif;
		
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
    	
    	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
    		if ($id != $this->get('security.context')->getToken()->getUser()->getId()):
    		   	throw $this->createNotFoundException('ERROR: No tiene permisos.');
    		endif;
    	endif;
    	
    	$userManager = $this->get('fos_user.user_manager');
    	
    	$entity = $userManager->findUserBy(array("id"=>$id));
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('ERROR: No se encontró el usuario.');
    	}
    
    	$editForm = $this->createForm(new ProfileUsuarioType(), $entity);
    	$deleteForm = $this->createDeleteForm($id);
    

    	
    	return $this->render('UsuarioBundle:Usuario:admin_edit.html.twig', array(
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
    	
    	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
    		throw $this->createNotFoundException('ERROR: No tiene permisos.');
    	endif;
    	$form = $this->createDeleteForm($id);
    	$request = $this->getRequest();
    
    	$form->bindRequest($request);
    
    	if ($form->isValid()) {
    		$em = $this->getDoctrine()->getEntityManager();
    		$entity = $em->getRepository('UsuarioBundle:Usuario')->find($id);
    
    		if (!$entity) {
    			throw $this->createNotFoundException('ERROR: No se econtró el usuario.');
    		}
    		
    		//LOG
    		$log = $this->get('log');
    		$log->setPrioridad(2);
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
    	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
    		throw $this->createNotFoundException('ERROR: No tiene permisos.');
    	endif;
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:list_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('usuario_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el usuario?'
    	));
    
    }
    
    public function accionDeleteformAction($id)
    {
    	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
    	throw $this->createNotFoundException('ERROR: No tiene permisos.');
    	endif;
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:accion_delete_form.html.twig', array(
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
    	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
    		throw $this->createNotFoundException('ERROR: No tiene permisos.');
    	endif;
        $userManager = $this->get('fos_user.user_manager');
        $entity = $userManager->createUser();
        
        $form   = $this->createForm(new RegistrationUsuarioNewType(), $entity);

        return $this->render('UsuarioBundle:Usuario:admin_new.html.twig', array(
            'entity' => $entity,
        		'errorContrasena' => "",
            'form'   => $form->createView()
        ));
    }

    
    /**
     * Creates a new Usuario entity.
     *
     */
     public function createAction()
     {
     	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
     		throw $this->createNotFoundException('ERROR: No tiene permisos.');
     	endif;
     	$userManager = $this->get('fos_user.user_manager');
     	$entity = $userManager->createUser();
     	$id = $entity->getId();
     	$request = $this->getRequest();
     	$form    = $this->createForm(new RegistrationUsuarioNewType(), $entity);
     	$form->bindRequest($request);
     	
     	$c = false;
     	$variables = $request->request->get("usuarioRegistration");
     	
		if ($variables["plainPassword"] != "") {
     		$c = true;
     	}

	 	if ($form->isValid() && $c) {
    		$entity->setEnabled(true);
     		$entity->addRole($entity->getPermisos());
    
     		$userManager->updateUser($entity);
    
     		$this->get('session')->setFlash('msj_info','El usuario se ha creado correctamente');
    
		    //LOG
		    $log = $this->get('log');
		    $log->setPrioridad(2);
		    $log->create($entity, "Usuario Creado");
		    
		    $editForm = $this->createForm(new ProfileUsuarioType(), $entity);
		    $deleteForm = $this->createDeleteForm($id);
		    return $this->redirect($this->generateUrl('usuario_edit', array(
		    'id' => $entity->getId(),
		    'edit_form'   => $editForm->createView(),
		    'delete_form' => $deleteForm->createView())));
		    
		     }
    
     	return $this->render('UsuarioBundle:Usuario:admin_new.html.twig', array(
     	'entity' => $entity,
     	'errorContrasena' => "Ingrese nuevamente la contraseña",
     	'form'   => $form->createView()
     	));
     }
     
     public function updateAction($id)
     {
     
     	$userManager = $this->get('fos_user.user_manager');
     
     	$entity = $userManager->findUserBy(array("id"=>$id));
          
     	if (!$entity) {
     		throw $this->createNotFoundException('ERROR: No se econtró el usuario.');
     	}
     
     	$editForm   = $this->createForm(new ProfileUsuarioType(), $entity);
     	$deleteForm = $this->createDeleteForm($id);
     
     	$request = $this->getRequest();
     
     	$editForm->bindRequest($request);
     
     	$em = $this->getDoctrine()->getEntityManager();
     
     
     
     	if ($editForm->isValid()) {
     		$userManager->updateUser($entity);
     
     		$this->get('session')->setFlash('msj_info','El usuario se ha modificado correctamente');
     		//LOG
     		$log = $this->get('log');
     		$log->create($entity, "Usuario Actualizado");
     
     		return $this->redirect($this->generateUrl('usuario_edit', array('id' => $entity->getId())));
     
     	}
     
     
     	return $this->render('UsuarioBundle:Usuario:admin_edit.html.twig', array(
     			
     			'entity'      => $entity,
     			'edit_form'   => $editForm->createView(),
     			'delete_form' => $deleteForm->createView(),
     	));
     }
     
     
     /**
      * Displays a form to change a Usuario pass.
      *
      */
     public function changePassAction($id)
     {
    
    	$userManager = $this->get('fos_user.user_manager');
    	
    	$entity = $userManager->findUserBy(array("id"=>$id));
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$changePassForm = $this->createForm(new ChangePassUsuarioType(), $entity);
 

    	
    	return $this->render('UsuarioBundle:Usuario:admin_changePass.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $changePassForm->createView(),
    	));
    }
    

    
    public function updatePassAction($id)
    {
    
    	$userManager = $this->get('fos_user.user_manager');
    
    	$entity = $userManager->findUserBy(array("id"=>$id));
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$editForm   = $this->createForm(new ChangePassUsuarioType(), $entity);
    	
    
    	$request = $this->getRequest();
    
    	$editForm->bindRequest($request);
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    
    
    	if ($editForm->isValid()) {
    		$userManager->updateUser($entity);
    
    		$this->get('session')->setFlash('msj_info','La contraseña se ha modificado correctamente');
    		
    		//LOG
    		$log = $this->get('log');
    		$log->setPrioridad(1);
    		$log->create($entity, "Contraseña actualizada a usuario");
    
    		return $this->redirect($this->generateUrl('usuario_edit', array('id' => $entity->getId())));
    
    	}
    
    
    	return $this->render('UsuarioBundle:Usuario:admin_changePass.html.twig', array(
    
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    	));
    }
    
    
    
    
    /**
     * Displays a form to change a Usuario permisos.
     *
     */
    public function cambiarPermisosAction($id)
    {
    	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
    	throw $this->createNotFoundException('No tiene permisos.');
    	endif;
    	$userManager = $this->get('fos_user.user_manager');
    
    	$entity = $userManager->findUserBy(array("id"=>$id));
    
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$changePassForm = $this->createForm(new CambiarPermisosUsuarioType(), $entity);
    
    
    
    	return $this->render('UsuarioBundle:Usuario:admin_cambiarPermisos.html.twig', array(
    			'entity'      => $entity,
    			'edit_form'   => $changePassForm->createView(),
    	));
    }
    
    
    public function updatePermisosAction($id)
    {
    	if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')):
    	throw $this->createNotFoundException('No tiene permisos.');
    	endif;
    	$userManager = $this->get('fos_user.user_manager');
    
    	$entity = $userManager->findUserBy(array("id"=>$id));
    
    	if (!$entity) {
    		throw $this->createNotFoundException('Unable to find User entity.');
    	}
    
    	$editForm   = $this->createForm(new CambiarPermisosUsuarioType(), $entity);
    
    
    	$request = $this->getRequest();
    
    	$editForm->bindRequest($request);
    
    	
    
    
    
    	if ($editForm->isValid()) {
    		$role[] = $entity->getPermisos();
    		$entity->setRoles($role);
    		$userManager->updateUser($entity);
    		
    		$this->get('session')->setFlash('msj_info','Los permisos se modificaron correctamente');
    		//LOG
    		$log = $this->get('log');
    		$log->setPrioridad(3);
    		
    		$log->create($entity, "Se modificaron los permisos de accesos a ''".$entity->getNombrePermiso()."'' al usuario");
    
    		return $this->redirect($this->generateUrl('usuario_edit', array('id' => $entity->getId())));
    
    	}
    
    
    	return $this->render('UsuarioBundle:Usuario:admin_cambiarPermisos.html.twig', array(
    
    			'entity'      => $entity,
    			'edit_form'   => $editForm->createView(),
    	));
    }
     
     
   
  
    

}
