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
    
	
	public function indexAction(){
		
		$cantidadDeDias=10;
		$dias=array();
		for($i=0;$i<$cantidadDeDias;$i++){
			$dias[]=strtotime('NOW + '.$i.' day');
		}
		
		
		$matrizMrpProductos=$this->realizarMRP(0);
		
		return $this->render('DecoyesoProduccionBundle:Mrp:admin_list.html.twig',array(
				'matrizMrpProductos'=>$matrizMrpProductos,
				'dias'=>$dias,
			)) ;
		
	}
	
	
    /**
     * Finds and displays a Proceso entity.
     *
     */
    
    public function showAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();    
    	$entity = $em->getRepository('ProductoBundle:Elemento')->find($id);
    	
    	$cantidadDeDias=10;
    	$dias=array();
    	for($i=0;$i<$cantidadDeDias;$i++){
    		$dias[]=strtotime('NOW + '.$i.' day');
    	}
    	
    	$matrizMRP=$this->realizarMRP($entity->getId());
    	
    	
		
    	return $this->render('DecoyesoProduccionBundle:Mrp:admin_show.html.twig', array(
    			'matrizMRP'      => $matrizMRP,
    			'entity'=>$entity,
    			'dias'=>$dias,
    	));
    }
    
 	
    private function realizarMRP($entityId=0){
    	
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$entity = $em->getRepository('ProductoBundle:Producto')->findBy(array(),array('tipo'=>'ASC','nombre'=>'ASC'));
    	$cantidadDeDias=10;
    	$matrizProductos=array();
    	$matrizMrpProductos = array();
    	
    	$cantidadProductosEnIntervalo=0;
    	foreach($entity as $producto){
    		if($producto->getCantidadSolicitadaStock()>0){
    	
    			$bSiEstaEnElIntervalo=0;
    			foreach ($producto->getSolicitudMovimientoElemento() as $solicitudesElementos){
    				$fechaHoraSolicitud=(int)$solicitudesElementos->getSolicitudMovimiento()->getFechaHoraRequerido()->format('z');
    				$hoyMasCantidadDiaz=(int)date('z',strtotime('+ 10 day'));
    					
    				if($fechaHoraSolicitud<=$hoyMasCantidadDiaz and $solicitudesElementos->getSolicitudMovimiento()->getEstado()==1){
    					$bSiEstaEnElIntervalo=1;
    				}
    					
    			}
    	
    			if($bSiEstaEnElIntervalo==1){
    				$matrizMrpProductos[$cantidadProductosEnIntervalo]['producto']=$producto;
    				$matrizMrpProductos[$cantidadProductosEnIntervalo]['mrp']=$this->realizarMRPIndividual($producto);
    				$cantidadProductosEnIntervalo++;
    			}
    	
    	
    		}
    	}
    	
    	    	
    	
    	// calculo las cantidades eop acumuldas por día
    	$arrayCantidadesEomAcumuladas=array();    	
    	foreach($matrizMrpProductos as $elementosMatrizMrpProductos =>$valorElementoMatrizMrpProductos ){
    		
    		foreach($valorElementoMatrizMrpProductos['mrp'] as $elementosEOP){
    			    			
    			if(array_key_exists($elementosEOP["numeroDia"],$arrayCantidadesEomAcumuladas)){
    				$arrayCantidadesEomAcumuladas[$elementosEOP["numeroDia"]]=$arrayCantidadesEomAcumuladas[$elementosEOP["numeroDia"]]+$elementosEOP["emisionOrdenPlanificada"];
    				
    			}else{
    				$arrayCantidadesEomAcumuladas[$elementosEOP["numeroDia"]]=$elementosEOP["emisionOrdenPlanificada"];
    			}
    			
    		}
    		
    	}
    	
    	
    	
    	// recalculo las eop por día
    	$cantidadReal=0;
    	$cantidadRealAcumuladaTotal=0;
    	$cantidadRealAcumuladaElemento=array();
    	$hoy=(int)date('z',strtotime('NOW'));
    	$hoyMacantidadDeDias=(int)date('z',strtotime('NOW + '.$cantidadDeDias.' DAY'))-1;
    	
    	
    	
    	for($d=$hoyMacantidadDeDias;$d>=$hoy;$d--){
    		
    		$arrayCantidadesEomAcumuladas[$d]=$arrayCantidadesEomAcumuladas[$d]+$cantidadRealAcumuladaTotal;
    		$cantidadRealAcumuladaTotal=0;
    		
    		for($j=0;$j<count($matrizMrpProductos);$j++){
    			
    			if($d<$hoyMacantidadDeDias-1){
    				
    				$matrizMrpProductos[$j]['mrp'][$d]['emisionOrdenPlanificada']=$matrizMrpProductos[$j]['mrp'][$d]['emisionOrdenPlanificada']+$cantidadRealAcumuladaElemento[$d+1][$j];
    			}
    			
    			
    			if($arrayCantidadesEomAcumuladas[$d]>240 and $d>$hoy){
    			
    				$cantidadReal=round(($matrizMrpProductos[$j]['mrp'][$d]['emisionOrdenPlanificada']/$arrayCantidadesEomAcumuladas[$d])*240);
    				$cantidadRealAcumuladaElemento[$d][$j]=($matrizMrpProductos[$j]['mrp'][$d]['emisionOrdenPlanificada']-$cantidadReal);
    				$cantidadRealAcumuladaTotal=$cantidadRealAcumuladaTotal+ ($matrizMrpProductos[$j]['mrp'][$d]['emisionOrdenPlanificada']-$cantidadReal);
    				
    				$matrizMrpProductos[$j]['mrp'][$d]['emisionOrdenPlanificada']=$cantidadReal;
    				 
    			}else{
    				$cantidadRealAcumuladaElemento[$d][$j]=0;
    			}
    			
    		}
    	}
    	
    	
    	
    	
    	if($entityId==0){
    		$valorRetornado=$matrizMrpProductos;
    	}else{
    		
    		for($i=0;$i<count($matrizMrpProductos);$i++){
    			if($matrizMrpProductos[$i]['producto']->getId()==$entityId){
    				$valorRetornado=$matrizMrpProductos[$i]['mrp'];
    				break;
    			}
    		}
    	}
    	
    	return $valorRetornado;
    	
    }
    
    
    private function realizarMRPIndividual($entity){
    	   	
    	
    	$numeroDeDiaActual=(int)date( 'z',strtotime('NOW'));
    	$cantidadDeDias=10;
    	$matrizMRP=array();
    	$numeroDeDiaProcesando=0;
    	$disponibilidad=0;
    	$necesidadNeta=0;
    	$stockSeguridad=$entity->getStockOptimo();
    	$capacidadProducción=240;
    	
    	
    	
    	
    	
    	for($i=0;$i<$cantidadDeDias;$i++){
    	
    		$matrizMRP[$numeroDeDiaActual+$i]['numeroDia']=$numeroDeDiaActual+$i;
    		$matrizMRP[$numeroDeDiaActual+$i]['necesidadBruta']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['disponibilidad']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['stockSeguridad']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['necesidadNeta']=0;
    		$matrizMRP[$numeroDeDiaActual+$i]['emisionOrdenPlanificada']=0;
    	
    	}
    	 
    	 
    	foreach ($entity->getSolicitudMovimientoElemento() as $solicitudMovimientoElemento){
    	
    		$numeroDeDiaProcesando=(int)$solicitudMovimientoElemento->getSolicitudMovimiento()->getFechaHoraRequerido()->format('z');
    	
    		if($solicitudMovimientoElemento->getSolicitudMovimiento()->getEstado()==1 and ($numeroDeDiaActual+$cantidadDeDias)>$numeroDeDiaProcesando){
    	
    			if($numeroDeDiaProcesando<=$numeroDeDiaActual){
    	
    				$matrizMRP[$numeroDeDiaActual]['necesidadBruta']=$matrizMRP[$numeroDeDiaActual]['necesidadBruta']+$solicitudMovimientoElemento->getCantidadSolicitada();
    					
    			}else{
    	
    				$matrizMRP[$numeroDeDiaProcesando]['necesidadBruta']=$matrizMRP[$numeroDeDiaProcesando]['necesidadBruta']+$solicitudMovimientoElemento->getCantidadSolicitada();
    					
    			}
    		}
    	}
    	
    	
    	$disponibilidad=$entity->getCantidadEnSTock();
    	
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
    	
    		if($disponibilidad>0){
    			$disponibilidad=$disponibilidad-$matrizMRP[$numeroDeDiaActual+$i]['necesidadBruta'];
    		}
    			
    			
    	}
    	
    	
    	$cantidadAProducir=0;
    	for($i=($cantidadDeDias-1);$i>=0;$i--){
    			
    		$cantidadAProducir=$cantidadAProducir+$matrizMRP[$numeroDeDiaActual+$i]['necesidadNeta'];
    	
    		if($i>3){
    				
    			if($cantidadAProducir<=240){
    				$matrizMRP[$numeroDeDiaActual+($i-3)]['emisionOrdenPlanificada']=$cantidadAProducir;
    				$cantidadAProducir=0;    				
    			}else{
    				$cantidadAProducir=$cantidadAProducir-240;
    					
    				$matrizMRP[$numeroDeDiaActual+($i-3)]['emisionOrdenPlanificada']=240;
    	
    			}
    	
    		}else{
    			$matrizMRP[$numeroDeDiaActual]['emisionOrdenPlanificada']=$cantidadAProducir;
    		}    		
    			
    			
    	}
    	
    	return $matrizMRP;
    }
    
    
	
}
