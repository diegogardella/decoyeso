<?php

namespace Decoyeso\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\ProductoBundle\Entity\ServicioInsumo
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ServicioInsumo
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Servicio")
     */
    private $servicio;

    /**
     * @ORM\Id
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
     * @param Decoyeso\ProductoBundle\Entity\Servicio $servicio
     */
    public function setServicio(\Decoyeso\ProductoBundle\Entity\Servicio $servicio)
    {
        $this->servicio = $servicio;
    }

    /**
     * Get producto
     *
     * @return Decoyeso\ProductoBundle\Entity\Servicio 
     */
    public function getServicio()
    {
        return $this->servicio;
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