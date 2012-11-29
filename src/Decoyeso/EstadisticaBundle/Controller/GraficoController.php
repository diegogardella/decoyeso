<?php

namespace Decoyeso\EstadisticaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\EstadisticaBundle\Entity\Grafico;

use Decoyeso\EstadisticaBundle\Form\GraficoType;


use DoctrineExtensions\Query\Mysql\Year;


/**
 * Grafico controller.
 *
 */
class GraficoController extends Controller
{
    /**
     * Lists all Grafico entities.
     *
     */
    public function indexAction()
    {
        
		$entities = "";
        return $this->render('DecoyesoEstadisticaBundle:Grafico:admin_index.html.twig', array(
            'entities' => $entities
        ));
    }

    
    public function productosFabricados() {
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	//$query = $em->createQuery('SELECT pp FROM DecoyesoProduccionBundle:ProcesoProducto pp');
    	$query = $em->createQuery('
    			SELECT pp FROM DecoyesoProduccionBundle:ProcesoProducto pp
    			JOIN pp.proceso pro
    			WHERE 
    			pro.fechaFin >= :pro_fechaDesde AND
    			pro.fechaFin <= :pro_fechaHasta 
    			');
    	
    	$query->setParameters(array(
    			'pro_fechaDesde' => "2012-11-28",
    			'pro_fechaHasta' => "2012-11-28",
    			
    	));
    	
    	$res = $query->getResult();	
    	
    	
		    
    	$grafico = new Grafico();
    
    	$valores = array();
    	/*
    	for($i=0; $i<count($res); $i++) {
    		$valores[$i]["label"] = $res[$i]->getProceso()->getNumero();
    		$valores[$i]["value"] = $res[$i]->getCantidad();
    	}
    	*/
    	for($i=0; $i<count($res); $i++) {
    		$valores[$i]["label"] = $res[$i]->getProducto()->getNombre();
    		$valores[$i]["value"] = $res[$i]->getCantidad();
    	}
    	
    	
    	$grafico->setValores($valores);
    	
    	$grafico->setTipo("Column3D");
    	$opciones = array(
    			'caption' => 'Cantidad de Productos',
    			'yAxisName' => 'Unidades',
    			'xAxisName' => 'Productos',
    			
    	);
    	$grafico->setOpciones($opciones);
    	
    	return $grafico;

    }
    

    

    public function mostrarGraficosModuloAction($modulo)
    {
    	
    	$fechas["hasta"] = new \DateTime('today');
    	$fechas["desde"] = $fechas["hasta"]->modify("-30 day");
    	
 
	   	switch ($modulo) {
    		case "proceso":
    			$graficos[1] =  $this->productosFabricados();
    			break;
    		default:
    			return $this->redirect($this->generateUrl('grafico'));
    	}
    	
    	return $this->render('DecoyesoEstadisticaBundle:Grafico:admin_show.html.twig', array(
    			'graficos'      => $graficos,
    			'modulo'	=> $modulo,
    			'fechas' => $fechas,
    			
    	));

    }
    
    public function mostrarGrafico($grafico) {
    	switch ($grafico) {
    		case 1:
    			$graficos[1] =  $this->productosFabricados();
    			break;
    		default:
    			return $this->redirect($this->generateUrl('grafico'));
    	}
    	
    	return $this->render('DecoyesoEstadisticaBundle:Grafico:admin_show.html.twig', array(
    			'graficos'      => $graficos,
    	
    	));
    }
    

}
