<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\ProduccionBundle\Entity\Proceso;
use Decoyeso\ProduccionBundle\Form\ProcesoType;
use Decoyeso\ProduccionBundle\Form\ProcesoFinalizarType;

use Decoyeso\ProduccionBundle\Entity\ProcesoProducto;
use Decoyeso\StockBundle\Entity\MovimientoStock;

/**
 * Proceso controller.
 *
 */
class MrpController extends Controller
{        
    
    /**
     * Finds and displays a Proceso entity.
     *
     */
    
    public function showAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();    
    	$entity = $em->getRepository('ProductoBundle:Elemento')->find($id);
    	$numeroDeDiaActual=(int)strtotime('NOW');
    	$cantidadDeDias=8;
    	$matrizMRP=array();
    	$numeroDeDiaProcesando=0;
    	$disponibilidad=0;
    	$necesidadNeta=0;
    	$stockSeguridad=0;
    	$capacidadProducción=240;
    	
    
    	if (!$entity) {
    		throw $this->createNotFoundException('ERROR: No se encontró el proceso.');
    	}
    
    	
    	for($i=0;$i<$cantidadDeDias;$i++){
    		
    		$matrizMRP[$numeroDeDiaActual+$i]['numeroSemana']=$numeroDeDiaActual+$i;
    		$matrizMRP[$numeroDeDiaActual+$i]['necesidadBruta']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['disponibilidad']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['stockSeguridad']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['necesidadNeta']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['emisionOrdenPlanificada']=0;
    		
    	}
    	
     	
		foreach ($entity->getSolicitudMovimientoElemento() as $solicitudMovimientoElemento){

			$numeroDeDiaProcesando=(int)$solicitudMovimientoElemento->getSolicitudMovimiento()->getFechaHoraRequerido()->format('W');
						
			if($solicitudMovimientoElemento->getSolicitudMovimiento()->getEstado()==1 and ($numeroDeDiaActual+$cantidadDeDias)>$numeroDeDiaProcesando){

				if($numeroDeDiaProcesando<=$numeroDeDiaActual){
										
					$matrizMRP[$numeroDeDiaActual]['necesidadBruta']=$matrizMRP[$numeroDeDiaActual]['necesidadBruta']+$solicitudMovimientoElemento->getCantidadSolicitada();
					
				}else{
										
					$matrizMRP[$numeroDeDiaProcesando]['necesidadBruta']=$matrizMRP[$numeroDeDiaProcesando]['necesidadBruta']+$solicitudMovimientoElemento->getCantidadSolicitada();
									
				}								
			}					
		}
		
		
		$disponibilidad=$entity->getCantidadEnSTock();		
		//$stockSeguridad=1000;
		
		for($i=0;$i<$cantidadDeDias;$i++){

			$necesidadNeta=($matrizMRP[$numeroDeDiaActual+$i]['necesidadBruta']+$stockSeguridad)-$disponibilidad;
			
			if($disponibilidad<0){
				$disponibilidad=0;
			}
			
			if($necesidadNeta<0){
				$necesidadNeta=0;
			}
			
			$matrizMRP[$numeroDeDiaActual+$i]['disponibilidad']=$disponibilidad;
			$matrizMRP[$numeroDeDiaActual+$i]['stockSeguridad']=$stockSeguridad;
			$matrizMRP[$numeroDeDiaActual+$i]['necesidadNeta']=$necesidadNeta;
			$matrizMRP[$numeroDeDiaActual+$i]['emisionOrdenPlanificada']=0;														
				
			$disponibilidad=$disponibilidad-$matrizMRP[$numeroDeDiaActual+$i]['necesidadBruta'];
			
		}
		
    	    
    	return $this->render('DecoyesoProduccionBundle:Mrp:admin_show.html.twig', array(
    			'matrizMRP'      => $matrizMRP,
    			'entity'=>$entity,
    	));
    }
    
 	
	
}
