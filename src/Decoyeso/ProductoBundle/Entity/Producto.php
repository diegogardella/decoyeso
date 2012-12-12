<?php

namespace Decoyeso\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;

/**
 * Decoyeso\ProductoBundle\Entity\Producto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Producto extends Elemento
{
	/**
	 * @ORM\OneToMany(targetEntity="Decoyeso\StockBundle\Entity\MovimientoStock", mappedBy="elemento")
	 */
	 private $movimientoStock;
	
	 
	 /**
	  *
	  * @ORM\OneToMany(targetEntity="ProductoInsumo", mappedBy="producto")
	  */
	 private $productoInsumo;
	 
	 
	 /**
	  * @var integer $elemento
	  *
	  * @ORM\OneToMany(targetEntity="Decoyeso\StockBundle\Entity\SolicitudMovimientoElemento", mappedBy="elemento")
	  */
	 private $solicitudMovimientoElemento;
	 
	
	/**
	 * @var integer $tipo
	 *
	 * @ORM\Column(name="tipo", type="integer")
	 */
	private $tipo;
	
	
	/**
	 * @var Molde $molde
	 *
	 * @ORM\OneToMany(targetEntity="\Decoyeso\ProduccionBundle\Entity\ProcesoProducto", mappedBy="producto" )
	 */
	private $proceso;

	
	
  
    
    public function getCantidadEnStock(){
    	
    	$cantidadProductoStock=0;
    	foreach($this->getMovimientoStock() as $productoStock){
    		if($productoStock->getAccion()==1){
    			$cantidadProductoStock=$cantidadProductoStock+$productoStock->getCantidad();
    		}else{
    			$cantidadProductoStock=$cantidadProductoStock-$productoStock->getCantidad();
    		}
    	}
    	return $cantidadProductoStock;
    	
    }
    
    
    public function getCantidadEntregadaStock(){
    
    	$cantidad=0;
    	foreach($this->getMovimientoStock() as $productoStock){
    		if($productoStock->getAccion()==2){
    			if($productoStock->getMotivo()==2){
    				$cantidad=$cantidad+$productoStock->getCantidad();
    			}
    		}else{
    			if($productoStock->getMotivo()==3){
    				$cantidad=$cantidad-$productoStock->getCantidad();
    			}
    		}
    	}
    
    	return $cantidad;
    }
    



    /**
     * Get solicitudMovimientoElemento
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSolicitudMovimientoElemento()
    {
        return $this->solicitudMovimientoElemento;
    }
    

    
    public function getCantidadSolicitadaStock($pedido=0){
    	    		
    		$cantidad=0;
    		
    		foreach($this->getSolicitudMovimientoElemento() as $solicitudMovimientoElemento){
    			
    			if($solicitudMovimientoElemento->getSolicitudMovimiento()->getEstado()==1 and $pedido==0){
    				$cantidad=$cantidad+$solicitudMovimientoElemento->getCantidadSolicitada();
    			}else{
    				
    				if($solicitudMovimientoElemento->getSolicitudMovimiento()->getEstado()==1 and $pedido==$solicitudMovimientoElemento->getSolicitudMovimiento()->getPedido()->getId()){

    					$cantidad=$cantidad+$solicitudMovimientoElemento->getCantidadSolicitada();    					
    				}
    					
    			}    			
    			 
    		}
    		

    	return $cantidad;    	 
    }
    
    public function getStockOptimo(){
    	
    	$arrayCantidadesOptimas=array();
    	$i=0;
    	$sumatoria=0;
    	$sumatoriaDesvio=0;
    	$xMenosMediaAlCuadrado=0;
    	$amplitudIntervalo=0;
    	$desvioEstandar=0;
    	$media=0;
    	
    	
    	
    	foreach($this->getSolicitudMovimientoElemento() as $solicitudMovimientoElemento){
    		
    		if($solicitudMovimientoElemento->getSolicitudMovimiento()->getEstado()==2){
    			
				if($solicitudMovimientoElemento->getSolicitudMovimiento()->getFechaHoraRequerido()->format('y')>=(date('y',strtotime('now -1 YEAR -2 MONTH')))){
					
    				$arrayCantidadesOptimas[$i]=$solicitudMovimientoElemento->getCantidadSolicitada();
    				$sumatoria=$sumatoria+$solicitudMovimientoElemento->getCantidadSolicitada();
    				$i=$i+1;
    				
    			}	
    		}
    	}
    	

    	
    	//nivelar muestra cuando existan menos de 10 muestras
    	if(count($arrayCantidadesOptimas)<10){
    		for($i=count($arrayCantidadesOptimas);$i<10;$i++){
    			$arrayCantidadesOptimas[$i]=0;
    		}	
    	}
    	
    	    	
    	if(count($arrayCantidadesOptimas)>0){
    		$media=$sumatoria/count($arrayCantidadesOptimas);
    		sort($arrayCantidadesOptimas, SORT_NUMERIC);
    	}
    	    	
	

    	for($k=0;$k<count($arrayCantidadesOptimas);$k++){
    		$sumatoriaDesvio=$sumatoriaDesvio+(pow(($arrayCantidadesOptimas[$k]-$media),2));
    	}
    	
    	if(count($arrayCantidadesOptimas)>0){
    		$desvioEstandar=round(sqrt($sumatoriaDesvio/count($arrayCantidadesOptimas)));
    	}

    	return round($desvioEstandar+$media);
    	
    }
    
    
    public function getCantidadEntregadaStockSolicitud($pedido=0){
    	
    	$cantidad=0;
    	
   		 foreach($this->getSolicitudMovimientoElemento() as $solicitudMovimientoElemento){
    			
    			if($solicitudMovimientoElemento->getSolicitudMovimiento()->getEstado()==2 and $pedido==0){
    				$cantidad=$cantidad+$solicitudMovimientoElemento->getMovimientoStock()->getCantidad();
    			}else{
    				
    				if($solicitudMovimientoElemento->getSolicitudMovimiento()->getEstado()==2 and $pedido==$solicitudMovimientoElemento->getSolicitudMovimiento()->getPedido()->getId()){

    					$cantidad=$cantidad+$solicitudMovimientoElemento->getMovimientoStock()->getCantidad();    					
    				}
    					
    			}    			
    			 
    		}
    	return $cantidad;
    	
    }
    

    public function __construct()
    {
    $this->movimientoStock = new \Doctrine\Common\Collections\ArrayCollection();
    $this->productoInsumo = new \Doctrine\Common\Collections\ArrayCollection();
    $this->solicitudMovimientoElemento = new \Doctrine\Common\Collections\ArrayCollection();
    $this->proceso = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set tipo
     *
     * @param integer $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
    
    public function getTipoNombre()
    {	
    	$tipo=array('Placas de Yeso','Molduras de Yeso','Otros');    	
    	return $tipo[$this->tipo];
    }

    /**
     * Add movimientoStock
     *
     * @param Decoyeso\StockBundle\Entity\MovimientoStock $movimientoStock
     */
    public function addMovimientoStock(\Decoyeso\StockBundle\Entity\MovimientoStock $movimientoStock)
    {
        $this->movimientoStock[] = $movimientoStock;
    }

    /**
     * Get movimientoStock
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMovimientoStock()
    {
        return $this->movimientoStock;
    }

    /**
     * Add productoInsumo
     *
     * @param Decoyeso\ProductoBundle\Entity\ProductoInsumo $productoInsumo
     */
    public function addProductoInsumo(\Decoyeso\ProductoBundle\Entity\ProductoInsumo $productoInsumo)
    {
        $this->productoInsumo[] = $productoInsumo;
    }

    /**
     * Get productoInsumo
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductoInsumo()
    {
        return $this->productoInsumo;
    }

    /**
     * Add solicitudMovimientoElemento
     *
     * @param Decoyeso\StockBundle\Entity\SolicitudMovimientoElemento $solicitudMovimientoElemento
     */
    public function addSolicitudMovimientoElemento(\Decoyeso\StockBundle\Entity\SolicitudMovimientoElemento $solicitudMovimientoElemento)
    {
        $this->solicitudMovimientoElemento[] = $solicitudMovimientoElemento;
    }

    /**
     * Add proceso
     *
     * @param Decoyeso\ProduccionBundle\Entity\ProcesoProducto $proceso
     */
    public function addProcesoProducto(\Decoyeso\ProduccionBundle\Entity\ProcesoProducto $proceso)
    {
        $this->proceso[] = $proceso;
    }

    /**
     * Get proceso
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProceso()
    {
        return $this->proceso;
    }
}