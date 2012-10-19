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
class ProductoProducto
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Producto")
     */
    private $producto;
    
    /**
     *@ORM\Id
     * @ORM\ManyToOne(targetEntity="Producto")
     */
    private $productos;
    
    /**
     *
     * @ORM\Column(name="cantidad", type="float")
     */
    private $cantidad;
    
   


    /**
     * Set producto
     *
     * @param Decoyeso\ProductoBundle\Entity\Producto $producto
     */
    public function setProducto(\Decoyeso\ProductoBundle\Entity\Producto $producto)
    {
        $this->producto = $producto;
    }

    /**
     * Get producto
     *
     * @return Decoyeso\ProductoBundle\Entity\Producto 
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set productos
     *
     * @param Decoyeso\ProductoBundle\Entity\Producto $productos
     */
    public function setProductos(\Decoyeso\ProductoBundle\Entity\Producto $productos)
    {
        $this->productos = $productos;
    }

    /**
     * Get productos
     *
     * @return Decoyeso\ProductoBundle\Entity\Producto 
     */
    public function getProductos()
    {
        return $this->productos;
    }

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
}