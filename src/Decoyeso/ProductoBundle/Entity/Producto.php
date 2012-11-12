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
	 *
	 * @ORM\OneToMany(targetEntity="ProductoInsumo", mappedBy="producto")
	 */
	private $productoInsumo;
	
	/**
	 * Get tipo
	 *
	 * @return integer
	 */
	public function getTipo()
	{
		return $this->tipo;
	}
	
	/**
	 * Set nombre
	 *
	 * @param string $nombre
	 */
	public function setTipo($tipo)
	{
		$this->tipo = $tipo;
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

	public function __construct()
	{
		$this->productoInsumo = new \Doctrine\Common\Collections\ArrayCollection();
		$this->movimientoStock = new \Doctrine\Common\Collections\ArrayCollection();
		$this->solicitudMovimientoElemento = new \Doctrine\Common\Collections\ArrayCollection();
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
    
    
    public function getCantidadEntregadaStock($pedido=0){
    	
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

    

}