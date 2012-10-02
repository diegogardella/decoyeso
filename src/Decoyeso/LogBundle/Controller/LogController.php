<?php

namespace Decoyeso\LogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\LogBundle\Entity\Log;
use Decoyeso\LogBundle\Form\LogType;

/**
 * Log controller.
 *
 */
class LogController extends Controller
{
    /**
     * Lists all Log entities.
     *
     */
	public function indexAction($pararouting="index")
	{
		$buscador=$this->get("buscador");
		$buscador->setRequest($this->getRequest());
		$buscador->setPararouting($pararouting);
	
		$buscador->setSql('SELECT l FROM LogBundle:Log l join l.usuario u order by l.fechaHoraCreado desc');
	
		$opciones=array(
				"u_nombre"=>array(null,array("label"=>"Nombre de usuario")),
				"u_apellido"=>array(null,array("label"=>"Apellido de usuario")),
				"l_log"=>array(null,array("label"=>"Acción")),
				"l_fechaActualizado"=>array("date",array("label"=>"Actualizado el","format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}',"empty_value"=>array("month"=>"Mes","year"=>"Año","day"=>"Día"))),
				
		);
		
		$buscador->setOpcionesForm($opciones);
	
		$resultados=$buscador->exeBuscar();
	
	
		return $this->render('LogBundle:Log:admin_list.html.twig', array(
				'entities' => $resultados["entities"],
				'formBuscar'=>$resultados["form"]->createView(),
		));
	
	
	}
    
    
    /**
     * Lists all Log entities.
     *
     */
    public function logsPorUsuarioAction($entity)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$entities = $em->getRepository('LogBundle:Log')->findAll();
    
    	return $this->render('LogBundle:Log:index.html.twig', array(
    			'entities' => $entities
    	));
    }

    /**
     * Finds and displays a Log entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('LogBundle:Log')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Log entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LogBundle:Log:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Log entity.
     *
     */
    public function newAction()
    {
        $entity = new Log();
        $form   = $this->createForm(new LogType(), $entity);

        return $this->render('LogBundle:Log:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
     * Creates a new Log entity.
     *
    */
     public function createAction($log)
     {
     	$user = $this->container->get('security.context')->getToken()->getUser();
     	$entity  = new Log();
     	$entity->setUsuario($user);
     	$entity->setLog($log);
     	$em = $this->getDoctrine()->getEntityManager();
     	$em->persist($entity);
     	$em->flush();
    
     	return "Ok";
    
     }
    
    
     

    /**
     * Creates a new Log entity.
     *
     
    public function createAction()
    {
        $entity  = new Log();
        $request = $this->getRequest();
        $form    = $this->createForm(new LogType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('log_show', array('id' => $entity->getId())));
            
        }

        return $this->render('LogBundle:Log:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
	*/
    /**
     * Displays a form to edit an existing Log entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('LogBundle:Log')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Log entity.');
        }

        $editForm = $this->createForm(new LogType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LogBundle:Log:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Log entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('LogBundle:Log')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Log entity.');
        }

        $editForm   = $this->createForm(new LogType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('log_edit', array('id' => $id)));
        }

        return $this->render('LogBundle:Log:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Log entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('LogBundle:Log')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Log entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('log'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
