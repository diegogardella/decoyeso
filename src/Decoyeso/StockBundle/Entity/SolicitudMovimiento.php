<?php

namespace Decoyeso\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\StockBundle\Entity\SolicitudMovimiento
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\StockBundle\Entity\SolicitudMovimientoRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @var integer $numero
     *
     * @ORM\Column(name="numero", type="string",length="12",nullable="true")
     */
    private $numero;

    /**
     * @var datetime $fechaHoraCreado
     *
     * @ORM\Column(name="fechaHoraCreado", type="datetime")
     */
    private $fechaHoraCreado;
    
    /**
     * @var datetime $fechaHoraCreado
     *
     * @ORM\Column(name="fechaHoraRequerido", type="datetime")
     */
    private $fechaHoraRequerido;


    /**
     * @var datetime $fechaHoraCierre
     *
     * @ORM\Column(name="fechaHoraCierre", type="datetime",nullable=true)
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
     * @var integer $direccionDestino
     *
     * @ORM\Column(name="direccionDestino", type="string",length="255",nullable="true")
     */
    private $direccionDestino;
    

    
    /**
     * @var integer $usuario
     *
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UsuarioBundle\Entity\Usuario")
     */
    private $usuarioCreo;
    
    /**
     * @var integer $usuario
     *
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UsuarioBundle\Entity\Usuario")
     */
    private $usuarioCerro;    
    
    

    
    /**
     * @ORM\postPersist
     */
    public function postPersist()
    {
    	$this->numero="SOL".str_pad($this->getId(),5,0,STR_PAD_LEFT);
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
    	$estado[1]='Solicitud Enviada';
    	$estado[2]='Solicitud Procesada';
    	$estado[3]='Solicitud Cancelada';
    	$estado[4]='Solicitud Cancelada, Cambió Presupuesto';
    	
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

    /**
     * Set usuarioCreo
     *
     * @param Decoyeso\UsuarioBundle\Entity\Usuario $usuarioCreo
     */
    public function setUsuarioCreo(\Decoyeso\UsuarioBundle\Entity\Usuario $usuarioCreo)
    {
        $this->usuarioCreo = $usuarioCreo;
    }

    /**
     * Get usuarioCreo
     *
     * @return Decoyeso\UsuarioBundle\Entity\Usuario 
     */
    public function getUsuarioCreo()
    {
        return $this->usuarioCreo;
    }

    /**
     * Set usuarioCerro
     *
     * @param Decoyeso\UsuarioBundle\Entity\Usuario $usuarioCerro
     */
    public function setUsuarioCerro(\Decoyeso\UsuarioBundle\Entity\Usuario $usuarioCerro)
    {
        $this->usuarioCerro = $usuarioCerro;
    }

    /**
     * Get usuarioCerro
     *
     * @return Decoyeso\UsuarioBundle\Entity\Usuario 
     */
    public function getUsuarioCerro()
    {
        return $this->usuarioCerro;
    }
    
    /**
     * Set numero
     *
     * @param string $numero
     */
    public function setNumero($numero)
    {
    	$this->numero = $numero;
    }
    
    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
    	return $this->numero;
    }
    

    /**
     * Set direccionDestino
     *
     * @param string $direccionDestino
     */
    public function setDireccionDestino($direccionDestino)
    {
        $this->direccionDestino = $direccionDestino;
    }

    /**
     * Get direccionDestino
     *
     * @return string 
     */
    public function getDireccionDestino()
    {
        return $this->direccionDestino;
    }
}