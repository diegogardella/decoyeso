<?php

namespace Decoyeso\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\StockBundle\Entity\SolicitudMovimiento
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\StockBundle\Entity\SolicitudMovimientoRepository")
 */
class SolicitudMovimiento
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
     * @var datetime $fechaHoraCreado
     *
     * @ORM\Column(name="fechaHoraCreado", type="date")
     */
    private $fechaHoraCreado;
    
    /**
     * @var datetime $fechaHoraCreado
     *
     * @ORM\Column(name="fechaHoraRequerido", type="date")
     */
    private $fechaHoraRequerido;

    /**
     * @var datetime $fechaHoraReserva
     *
     * @ORM\Column(name="fechaHoraReserva", type="date",nullable=true)
     */
    private $fechaHoraReserva;

    /**
     * @var datetime $fechaHoraCierre
     *
     * @ORM\Column(name="fechaHoraCierre", type="date",nullable=true)
     */
    private $fechaHoraCierre;

    /**
     * @var integer $estado
     *
     * @ORM\Column(name="estado", type="integer")
     */
    private $estado;
    
    
    /**
     * @var integer $solicitudMovimientoElemento
     *
     * @ORM\OneToMany(targetEntity="SolicitudMovimientoElemento",mappedBy="solicitudMovimiento")
     */    
    private $solicitudMovimientoElemento;

    
    /**
     * @var integer $pedido
     *
     * @ORM\ManyToOne(targetEntity="Decoyeso\PedidoBundle\Entity\Pedido",inversedBy="$solicitudMovimiento")
     */
    private $pedido;


    /**
     * @var text $observacion
     *
     * @ORM\Column(name="observacion", type="text",nullable=true)
     */
    private $observacion;
    

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
     * Set fechaHoraCreado
     *
     * @param datetime $fechaHoraCreado
     */
    public function setFechaHoraCreado($fechaHoraCreado)
    {
        $this->fechaHoraCreado = $fechaHoraCreado;
    }

    /**
     * Get fechaHoraCreado
     *
     * @return datetime 
     */
    public function getFechaHoraCreado()
    {
        return $this->fechaHoraCreado;
    }

    /**
     * Set fechaHoraReserva
     *
     * @param datetime $fechaHoraReserva
     */
    public function setFechaHoraReserva($fechaHoraReserva)
    {
        $this->fechaHoraReserva = $fechaHoraReserva;
    }

    /**
     * Get fechaHoraReserva
     *
     * @return datetime 
     */
    public function getFechaHoraReserva()
    {
        return $this->fechaHoraReserva;
    }

    /**
     * Set fechaHoraCierre
     *
     * @param datetime $fechaHoraCierre
     */
    public function setFechaHoraCierre($fechaHoraCierre)
    {
        $this->fechaHoraCierre = $fechaHoraCierre;
    }

    /**
     * Get fechaHoraCierre
     *
     * @return datetime 
     */
    public function getFechaHoraCierre()
    {
        return $this->fechaHoraCierre;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Get estado
     *
     * @return integer 
     */
    public function getEstado()
    {
        return $this->estado;
    }
    
    public function getEstadoNombre()
    {	
    	$estado[1]='Enviada a stock';
    	$estado[2]='Reserva realizada';
    	
    	return $estado[$this->estado];
    }

    /**
     * Set pedido
     *
     * @param integer $pedido
     */
    public function setPedido($pedido)
    {
        $this->pedido = $pedido;
    }

    /**
     * Get pedido
     *
     * @return integer 
     */
    public function getPedido()
    {
        return $this->pedido;
    }
    public function __construct()
    {
        $this->solicitudMovimientoElemento = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set fechaHoraRequerido
     *
     * @param datetime $fechaHoraRequerido
     */
    public function setFechaHoraRequerido($fechaHoraRequerido)
    {
        $this->fechaHoraRequerido = $fechaHoraRequerido;
    }

    /**
     * Get fechaHoraRequerido
     *
     * @return datetime 
     */
    public function getFechaHoraRequerido()
    {
        return $this->fechaHoraRequerido;
    }

    /**
     * Set observacion
     *
     * @param text $observacion
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * Get observacion
     *
     * @return text 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }
}