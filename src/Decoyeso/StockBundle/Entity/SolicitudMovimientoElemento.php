<?php

namespace Decoyeso\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\StockBundle\Entity\SolicitudMovimientoElemento
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\StockBundle\Entity\SolicitudMovimientoElementoRepository")
 */
class SolicitudMovimientoElemento
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
     * @var float $cantidadSolicitada
     *
     * @ORM\Column(name="cantidadSolicitada", type="float")
     */
    private $cantidadSolicitada;

    /**
     * @var integer $elemento
     *
     * @ORM\ManyToOne(targetEntity="Decoyeso\ProductoBundle\Entity\Elemento")
     */
    private $elemento;

    /**
     * @var integer $solicitudMovimiento
     *
     * @ORM\ManyToOne(targetEntity="SolicitudMovimiento", inversedBy="solicitudMovimientoElemento")
     */
    private $solicitudMovimiento;

    /**
     * @var integer $movimientoStock
     *
     * @ORM\OneToOne(targetEntity="MovimientoStock")
     */
    private $movimientoStock;
	

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
     * Set cantidadSolicitada
     *
     * @param float $cantidadSolicitada
     */
    public function setCantidadSolicitada($cantidadSolicitada)
    {
        $this->cantidadSolicitada = $cantidadSolicitada;
    }

    /**
     * Get cantidadSolicitada
     *
     * @return float 
     */
    public function getCantidadSolicitada()
    {
        return $this->cantidadSolicitada;
    }

    /**
     * Set cantidadReservada
     *
     * @param float $cantidadReservada
     */
    public function setCantidadReservada($cantidadReservada)
    {
        $this->cantidadReservada = $cantidadReservada;
    }

    /**
     * Get cantidadReservada
     *
     * @return float 
     */
    public function getCantidadReservada()
    {
        return $this->cantidadReservada;
    }

    /**
     * Set elemento
     *
     * @param integer $elemento
     */
    public function setElemento(\Decoyeso\ProductoBundle\Entity\Elemento $elemento)
    {
        $this->elemento = $elemento;
    }

    /**
     * Get elemento
     *
     * @return integer 
     */
    public function getElemento()
    {
        return $this->elemento;
    }

    /**
     * Set solicitudMovimiento
     *
     * @param integer $solicitudMovimiento
     */
    public function setSolicitudMovimiento($solicitudMovimiento)
    {
        $this->solicitudMovimiento = $solicitudMovimiento;
    }

    /**
     * Get solicitudMovimiento
     *
     * @return integer 
     */
    public function getSolicitudMovimiento()
    {
        return $this->solicitudMovimiento;
    }

    /**
     * Set movimientoStock
     *
     * @param integer $movimientoStock
     */
    public function setMovimientoStock($movimientoStock)
    {
        $this->movimientoStock = $movimientoStock;
    }

    /**
     * Get movimientoStock
     *
     * @return integer 
     */
    public function getMovimientoStock()
    {
        return $this->movimientoStock;
    }
}