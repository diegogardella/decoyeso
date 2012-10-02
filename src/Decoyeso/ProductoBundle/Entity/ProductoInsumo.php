<?php

namespace Decoyeso\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\ProductoBundle\Entity\ProductoInsumo
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductoInsumo
{
    /**
     *@ORM\Id
     * @ORM\ManyToOne(targetEntity="Producto")
     */
    private $producto;

    /**
     *
     *@ORM\Id
     * @ORM\ManyToOne(targetEntity="Insumo")
     */
    private $insumo;

    /**
     *
     * @ORM\Column(name="cantidad", type="float")
     */
    private $cantidad;


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
     * Set insumo
     *
     * @param Decoyeso\ProductoBundle\Entity\Insumo $insumo
     */
    public function setInsumo(\Decoyeso\ProductoBundle\Entity\Insumo $insumo)
    {
        $this->insumo = $insumo;
    }

    /**
     * Get insumo
     *
     * @return Decoyeso\ProductoBundle\Entity\Insumo 
     */
    public function getInsumo()
    {
        return $this->insumo;
    }
}