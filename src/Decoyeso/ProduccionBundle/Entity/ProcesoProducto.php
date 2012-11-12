<?php

namespace Decoyeso\ProduccionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\ProduccionBundle\Entity\ProcesoProducto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProcesoProducto
{
	
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
     /**
     * 
     * @ORM\ManyToOne(targetEntity="Decoyeso\ProductoBundle\Entity\Producto")
     */
    private $producto;

    /**
     *
     *
     * @ORM\ManyToOne(targetEntity="Proceso")
     */
    private $proceso;

    /**
     *
     * @ORM\Column(name="cantidad", type="float")
     */
    private $cantidad;
    
    /**
     *
     * @ORM\Column(name="cantidadProducida", type="float")
     */
    private $cantidadProducida;
    
    /**
     *
     * @ORM\Column(name="cantidadIngresadaStock", type="float")
     */
    private $cantidadIngresadaStock;

    /**
     * Set cantidad
     *
     * @param float $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * Get cantidad
     *
     * @return float 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set producto
     *
     * @param  Decoyeso\ProductoBundle\Entity\Producto $producto
     */
    public function setProducto(\Decoyeso\ProductoBundle\Entity\Producto $producto)
    {
        $this->producto = $producto;
    }

    /**
     * Get producto
     *
     * @return  Decoyeso\ProductoBundle\Entity\Producto 
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set proceso
     *
     * @param Decoyeso\ProduccionBundle\Entity\Proceso $proceso
     */
    public function setProceso(\Decoyeso\ProduccionBundle\Entity\Proceso $proceso)
    {
        $this->proceso = $proceso;
    }

    /**
     * Get proceso
     *
     * @return Decoyeso\ProduccionBundle\Entity\Proceso 
     */
    public function getProceso()
    {
        return $this->proceso;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cantidadProducida
     *
     * @param float $cantidadProducida
     */
    public function setCantidadProducida($cantidadProducida)
    {
        $this->cantidadProducida = $cantidadProducida;
    }

    /**
     * Get cantidadProducida
     *
     * @return float 
     */
    public function getCantidadProducida()
    {
        return $this->cantidadProducida;
    }

    /**
     * Set cantidadIngresadaStock
     *
     * @param float $cantidadIngresadaStock
     */
    public function setCantidadIngresadaStock($cantidadIngresadaStock)
    {
        $this->cantidadIngresadaStock = $cantidadIngresadaStock;
    }

    /**
     * Get cantidadIngresadaStock
     *
     * @return float 
     */
    public function getCantidadIngresadaStock()
    {
        return $this->cantidadIngresadaStock;
    }
}