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
    public function indexAction($pararouting)
    {
        $buscador=$this->get("buscador");
    	$buscador->setRequest($this->getRequest());
    	$buscador->setPararouting($pararouting);
    
    	$buscador->setSql('SELECT p FROM ProductoBundle:'.ucfirst(strtolower($pararouting)).' p ORDER BY p.nombre ASC');
    
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
    

        return $this->render('StockBundle:MovimientoStock:admin_list.html.twig', array(
            	'entities' => $resultados["entities"],
    			'pararouting'=>$pararouting,
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
