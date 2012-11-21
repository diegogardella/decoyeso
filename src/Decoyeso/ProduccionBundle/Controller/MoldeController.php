<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProduccionBundle\Entity\Molde;
use Decoyeso\ProduccionBundle\Form\MoldeType;

/**
 * Molde controller.
 *
 */
class MoldeController extends Controller
{
    /**
     * Lists all Molde entities.
     *
     */
    public function indexAction($pararouting="index")
    {
             
        $buscador=$this->get("buscador");
        $buscador->setRequest($this->getRequest());
        $buscador->setPararouting($pararouting);
        
        $buscador->setSql('SELECT p FROM DecoyesoProduccionBundle:Molde p order BY p.id DESC');
        
        $opciones=array(
        		"p_nombre"=>array(null,array("label"=>"Molde")),
        
        );
        
        $buscador->setOpcionesForm($opciones);
        
        $resultados=$buscador->exeBuscar();
        
        
        return $this->render('DecoyesoProduccionBundle:Molde:admin_list.html.twig', array(
        		'entities' => $resultados["entities"],
        		'formBuscar'=>$resultados["form"]->createView(),
        ));
    }

    /**
     * Finds and displays a Molde entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Molde')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('ERROR: no se encontró el molde.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Molde:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Molde entity.
     *
     */
    public function newAction()
    {
        $entity = new Molde();
        $form   = $this->createForm(new MoldeType(), $entity);

        return $this->render('DecoyesoProduccionBundle:Molde:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a new Molde entity.
     *
     */
    public function createAction()
    {
        $entity  = new Molde();
        $request = $this->getRequest();
        $form    = $this->createForm(new MoldeType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            $log = $this->get('log');
            $log->create($entity, "Molde creado");
            
            $this->get('session')->setFlash('msj_info','El molde se ha creado correctamente.');

            return $this->redirect($this->generateUrl('molde_edit', array('id' => $entity->getId())));
            
        }

        return $this->render('DecoyesoProduccionBundle:Molde:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Displays a form to edit an existing Molde entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Molde')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('ERROR: no se encontró el molde.');
        }

        $editForm = $this->createForm(new MoldeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DecoyesoProduccionBundle:Molde:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Molde entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('DecoyesoProduccionBundle:Molde')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('ERROR: no se encontró el molde.');
        }

        $editForm   = $this->createForm(new MoldeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $log = $this->get('log');
            $log->create($entity, "Molde actualizado");
            
            $this->get('session')->setFlash('msj_info','El molde se ha actualizado correctamente.');

            return $this->redirect($this->generateUrl('molde_edit', array('id' => $id)));
        }

        return $this->render('DecoyesoProduccionBundle:Molde:admin_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Molde entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('DecoyesoProduccionBundle:Molde')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('ERROR: no se encontró el molde.');
            }
            
            //LOG
            $log = $this->get('log');
            $log->create($entity, "Molde eliminado");

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('molde'));
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
    			'url' => $this->generateUrl('molde_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el molde?'
    	));
    
    }
    
    public function accionDeleteformAction($id)
    {
    	$deleteForm = $this->createDeleteForm($id);
    
    	return $this->render('CoobixAdminBundle:Default:accion_delete_form.html.twig', array(
    			'delete_form' => $deleteForm->createView(),
    			'url' => $this->generateUrl('molde_delete', array('id' => $id)),
    			'id'=>$id,
    			'msj'=>'¿Seguro desea eliminar el molde?'
    	));
    
    }
    
}
