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
		
		$todas = 0;
		$prioridad = 0;
		
		$request = $this->getRequest();
	
			if ($request->get("todas") == 1) {
				$prioridad = -1;
			}

		
		
		$buscador->setSql('SELECT l FROM LogBundle:Log l join l.usuario u 
				WHERE l.prioridad != '.$prioridad.' 
				order by l.fechaHoraCreado DESC');
	
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
     * Displays a form to create a new Mesa entity.
     *
     */
    public function newAction()
    {
    	$entity = $this->get('log');
    	$form   = $this->createForm(new LogType(), $entity);
    
    	return $this->render('LogBundle:Log:admin_new.html.twig', array(
    			'entity' => $entity,
    			'form'   => $form->createView()
    	));
    }
    
    /**
     * Creates a new Mesa entity.
     *
     */
    public function createAction()
    {
    	$entity = $this->get('log');
    	$request = $this->getRequest();
    	$form    = $this->createForm(new LogType(), $entity);
    	$form->bindRequest($request);
    
    	if ($form->isValid()) {
    		$entity->setUsuario($this->container->get('security.context')->getToken()->getUser());
    		$em = $this->getDoctrine()->getEntityManager();
    		$em->persist($entity);
    		$em->flush();
    

    		return $this->redirect($this->generateUrl('log'));
    
    	}
    
    	return $this->render('LogBundle:Log:admin_new.html.twig', array(
    			'entity' => $entity,
    			'form'   => $form->createView()
    	));
    }

    

}
