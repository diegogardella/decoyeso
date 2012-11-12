<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProduccionBundle\Entity\Secador;
use Decoyeso\ProduccionBundle\Form\SecadorType;
use Decoyeso\ProduccionBundle\Entity\LugarSecador;

/**
 * Secador controller.
 *
 */
class SecadorController extends Controller
{
    /**
     * Lists all Secador entities.
     *
     */
    public function indexAction($pararouting="index")
    {
        $buscador=$this->get("buscador");
        $buscador->setRequest($this->getRequest());
        $buscador->setPararouting($pararouting);
        
        $buscador->setSql('SELECT p FROM DecoyesoProduccionBundle:Secador p order BY p.id DESC');
        
        $opciones=array(
        		"p_nombre"=>array(null,array("label"=>"Secador")),
        
        );
        
        $buscador->setOpcionesForm($opciones);
        
        $resultados=$buscador->exeBuscar();
        
        
        return $this->render('DecoyesoProduccionBundle:Secador:admin_list.html.twig', array(
        		'entities' => $resultados["entities"],
        		'formBuscar'=>$resultados["form"]->createView(),
        ));
    }
    
    public function estadoAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
        		
    	//Traigo secadores de placas
    	$query = $em->createQuery('SELECT se FROM DecoyesoProduccionBundle:Secador se
    			WHERE se.tipo = :se_tipo
    			');
    	$query->setParameters(array(
    			'se_tipo' => 0,
    	));
    	$secadoresPlacas = $query->getResult();
    	
    	
    	//Traigo secadores de molduras
    	$query = $em->createQuery('SELECT se FROM DecoyesoProduccionBundle:Secador se
    			WHERE se.tipo = :se_tipo
    			');
    	$query->setParameters(array(
    			'se_tipo' => 1,
    	));
    	$secadoresMolduras = $query->getResult();
    
    	$request = $this->getRequest();
    	
    	$filtro = 0;
    	$tipo = 0;
    
    	
    	if ($request->query->has("filtro")) {
    		$filtro = $request->get("filtro");
    	}
    	
    	if ($request->query->has("tipo")) {
    		$tipo = $request->get("tipo");
    	}
    	
    	if ($tipo == 0 ) {
	    	return $this->render('DecoyesoProduccionBundle:Secador:admin_estado.html.twig', array(
	    			'secadoresPlacas' => $secadoresPlacas,
	    			'secadoresMolduras' => $secadoresMolduras,
	    			'filtro' => $filtro,
	    			'tipo' => $tipo,
	    			
	    	));	
    	}
    	
    	else {
    		return $this->render('DecoyesoProduccionBundle:Secador:admin_estado.html.twig', array(
    				'secadoresPlacas' => $secadoresPlacas,
    				'secadoresMolduras' => $secadoresMolduras,
    				'filtro' => $filtro,
    				'tipo' => $tipo,
    		));
    	}
    	
    	
    }
    
    
   
    
    
    public function estadoSecadorAction($secadores)
    {
    	//Si es secador de placas
    	if ($secador->getTipo() == 0) {
    		return $this->render('DecoyesoProduccionBundle:Secador:secador_placas.html.twig', array(
    				'secadores' => $secadores,
    		));
    	}
    	//Si es secador de Molduras
    	if ($secador->getTipo() == 1) {
    		return $this->render('DecoyesoProduccionBundle:Secador:secador_molduras.html.twig', array(
    				'secadores' => $secadores,
    		));
    	}
    	
    }
    

    public function disponibilidadSecadoresAction($datos)
    {
    	$totalCantidadPlacas = $datos["totalCantidadPlacas"];	
    	$totalCantidadMolduras = $datos["totalCantidadMolduras"];
    	
    	$cantidadLugaresLibresEnSecadoresPlacas = $this->cantidadLugaresLibresEnSecadores(0);
    	$cantidadLugaresLibresEnSecadoresMolduras = $this->cantidadLugaresLibresEnSecadores(1);
    	
    	return $this->render('DecoyesoProduccionBundle:Secador:cuadro_disponibilidad_secadores.html.twig', array(
    			'totalCantidadPlacas' => $totalCantidadPlacas,
    			'totalCantidadMolduras' => $totalCantidadMolduras,
    			'cantidadLugaresLibresEnSecadoresPlacas' => $cantidadLugaresLibresEnSecadoresPlacas,
    			'cantidadLugaresLibresEnSecadoresMolduras' => $cantidadLugaresLibresEnSecadoresMolduras,
    	));
    }
    
    
    public function cantidadLugaresLibresEnSecadores($tipo) {
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$cantidad = 0;
    	
    	$query = $em->createQuery('SELECT COUNT(lu.id) FROM DecoyesoProduccionBundle:LugarSecador lu
    			JOIN lu.secador se
    			WHERE lu.disponible = :lu_disponible AND
    			se.tipo = :se_tipo
    			');
    	$query->setParameters(array(
    			'lu_disponible' => 0,
    			'se_tipo' => $tipo,
    			
    	));
    	$cantidad = $query->getSingleScalarResult();
    	
    	return $cantidad;
    }
    
    public function cantidadLugaresLibreEnSecadore($idSecador) {
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$cantidad = 0;
    
    	$query = $em->createQuery('SELECT COUNT(lu.id) FROM DecoyesoProduccionBundle:LugarSecador lu
    			JOIN lu.secador se
    			WHERE se.id = :se_id AND
    			lu.disponible = :lu_disponible
    			
    			');
    	$query->setParameters(array(
    			'lu_disponible' => 0,
    			'se_id' => $idSecador,
    
    	));
    	$cantidad = $query->getSingleScalarResult();
    
    	return $cantidad;
    }
    
    

    /**
     * Finds and displays a Secador entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Secador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Secador entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Secador:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Secador entity.
     *
     */
    public function newAction()
    {
        $entity = new Secador();
        $form   = $this->createForm(new SecadorType(), $entity);

        return $this->render('DecoyesoProduccionBundle:Secador:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Secador entity.
     *
     */
    public function createAction()
    {
        $entity  = new Secador();
        $request = $this->getRequest();
        $form    = $this->createForm(new SecadorType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            for ($i=1; $i<=$entity->getCapacidad(); $i++):
            	$lugarSecador = new LugarSecador();
            	$lugarSecador->setNombre("Lugar ".$i);
            	$lugarSecador->setDisponible(0);
            	$lugarSecador->setSecador($entity);
            	$em->persist($lugarSecador);
            	$entity->addLugarSecador($lugarSecador);
            endfor;
            $em->persist($entity);
            $em->flush();
            $log = $this->get('log');
            $log->create($entity, "Secador Creado");
            
            $this->get('session')->setFlash('msj_info','El secador se ha creado correctamente');

            return $this->redirect($this->generateUrl('secador_edit', array('id' => $entity->getId())));
            
        }

        return $this->render('DecoyesoProduccionBundle:Secador:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Secador entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Secador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Secador entity.');
        }

        $editForm = $this->createForm(new SecadorType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Secador:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Secador entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Secador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Secador entity.');
        }

        $editForm   = $this->createForm(new SecadorType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $log = $this->get('log');
            $log->create($entity, "Secador Actualizado");
            
            $this->get('session')->setFlash('msj_info','El Secador se ha actualizado correctamente');

            return $this->redirect($this->generateUrl('secador_edit', array('id' => $id)));
        }

        return $this->render('DecoyesoProduccionBundle:Secador:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Secador entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('DecoyesoProduccionBundle:Secador')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Secador entity.');
            }

            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Secador Eliminado");
            
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('secador'));
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
    			'url' => $this->generateUrl('secador_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el secador?'
    	));
    
    }
}
