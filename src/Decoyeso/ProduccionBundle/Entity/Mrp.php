<?php

namespace Decoyeso\ProduccionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\ProduccionBundle\Entity\Mrp
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Mrp
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
     * @var float $necesidadesBrutas
     *
     * @ORM\Column(name="necesidadesBrutas", type="float")
     */
    private $necesidadesBrutas;

    /**
     * @var float $disponibilidad
     *
     * @ORM\Column(name="disponibilidad", type="float")
     */
    private $disponibilidad;

    /**
     * @var float $stockSeguridad
     *
     * @ORM\Column(name="stockSeguridad", type="float")
     */
    private $stockSeguridad;

    /**
     * @var float $necesidadNeta
     *
     * @ORM\Column(name="necesidadNeta", type="float")
     */
    private $necesidadNeta;

    /**
     * @var float $emisionOrdenPedido
     *
     * @ORM\Column(name="emisionOrdenPedido", type="float")
     */
    private $emisionOrdenPedido;


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
     * Set necesidadesBrutas
     *
     * @param float $necesidadesBrutas
     */
    public function setNecesidadesBrutas($necesidadesBrutas)
    {
        $this->necesidadesBrutas = $necesidadesBrutas;
    }

    /**
     * Get necesidadesBrutas
     *
     * @return float 
     */
    public function getNecesidadesBrutas()
    {
        return $this->necesidadesBrutas;
    }

    /**
     * Set disponibilidad
     *
     * @param float $disponibilidad
     */
    public function setDisponibilidad($disponibilidad)
    {
        $this->disponibilidad = $disponibilidad;
    }

    /**
     * Get disponibilidad
     *
     * @return float 
     */
    public function getDisponibilidad()
    {
        return $this->disponibilidad;
    }

    /**
     * Set stockSeguridad
     *
     * @param float $stockSeguridad
     */
    public function setStockSeguridad($stockSeguridad)
    {
        $this->stockSeguridad = $stockSeguridad;
    }

    /**
     * Get stockSeguridad
     *
     * @return float 
     */
    public function getStockSeguridad()
    {
        return $this->stockSeguridad;
    }

    /**
     * Set necesidadNeta
     *
     * @param float $necesidadNeta
     */
    public function setNecesidadNeta($necesidadNeta)
    {
        $this->necesidadNeta = $necesidadNeta;
    }

    /**
     * Get necesidadNeta
     *
     * @return float 
     */
    public function getNecesidadNeta()
    {
        return $this->necesidadNeta;
    }

    /**
     * Set emisionOrdenPedido
     *
     * @param float $emisionOrdenPedido
     */
    public function setEmisionOrdenPedido($emisionOrdenPedido)
    {
        $this->emisionOrdenPedido = $emisionOrdenPedido;
    }

    /**
     * Get emisionOrdenPedido
     *
     * @return float 
     */
    public function getEmisionOrdenPedido()
    {
        return $this->emisionOrdenPedido;
    }
}