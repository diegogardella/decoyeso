<?php

namespace Decoyeso\StockBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\StockBundle\Entity\MovimientoStock;
use Decoyeso\StockBundle\Form\MovimientoStockProductoType;
use Decoyeso\StockBundle\Form\MovimientoStockInsumoType;

/**
 * MovimientoStock cProductoller.
 *
 */
class MovimientoStockController extends Controller
{
    /**
     * Lists all MovimientoStock entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('StockBundle:MovimientoStock')->findAll();

        return $this->render('StockBundle:MovimientoStock:index.html.twig', array(
            'entities' => $entities
        ));
    }



    /**
     * Displays a form to create a new MovimientoStock entity.
     *
     */
    public function newAction($accion,$tipo)
    {
        $entity = new MovimientoStock();
        if($tipo=="1"){
        	$form= $this->createForm(new MovimientoStockProductoType(), $entity);
        }else{
        	$form= $this->createForm(new MovimientoStockInsumoType(), $entity);
        }

        return $this->render('StockBundle:MovimientoStock:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        	'accion'=>$accion,
        	'tipo'=>$tipo
        ));
    }

    /**
     * Creates a new MovimientoStock entity.
     *
     */
    public function createAction($tipo)
    {
        $entity  = new MovimientoStock();
        $request = $this->getRequest();
    	
    	if($tipo=="1"){
        	$form= $this->createForm(new MovimientoStockProductoType(), $entity);
        }else{
        	$form= $this->createForm(new MovimientoStockInsumoType(), $entity);
        }
        
        $form->bindRequest($request);

        if ($form->isValid()) {
        	
           $em = $this->getDoctrine()->getEntityManager();
           $usu=$em->getRepository('UsuarioBundle:Usuario')->find(3);
            
            $entity->setUsuario($usu);
            $entity->setFechaHora(new \DateTime());            
            
            $em->persist($entity);
            $em->flush();

            if($tipo=="1"){
            	$ruta='producto_edit';
            }else{
            	$ruta='insumo_edit';
            }
            

            
            return $this->redirect($this->generateUrl($ruta, array('id' => $entity->getElemento()->getId())));
            
        }

        return $this->render('StockBundle:MovimientoStock:admin_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    
    
}
