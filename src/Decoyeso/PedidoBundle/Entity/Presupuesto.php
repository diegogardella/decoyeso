<?php

namespace Decoyeso\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Decoyeso\PedidoBundle\Entity\Presupuesto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\PedidoBundle\Entity\PresupuestoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Presupuesto
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
     * @var string $numero
     *
     * @ORM\Column(name="numero", type="string", length=12, nullable="true")
     */
    private $numero;
    
    /**
     * @var string $nombre
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
       
    /**
     * @var float $subTotal
     * @Assert\NotBlank()
     * @ORM\Column(name="subTotal", type="float")
     */
    private $subTotal;
    
    /**
     * @var float $total
     * @Assert\NotBlank()
     * @ORM\Column(name="total", type="float")
     */
    private $total;
    
    /**
     * @var float $precioEntrega
     * @ORM\Column(name="precioEntrega", type="float", nullable="true")
     */
    private $precioEntrega;
    
    /**
     * @var string $precioTextoEntrega
     * @Assert\MaxLength(255)
     * @ORM\Column(name="precioTextoEntrega", type="string", length=255, nullable="true")
     */
    private $precioTextoEntrega;
    
    /**
     * @var float $precioSaldo
     * @ORM\Column(name="precioSaldo", type="float", nullable="true")
     */
    private $precioSaldo;
    
    /**
     * @var string $formaPago
     * @Assert\MaxLength(255)
     * @ORM\Column(name="formaPago", type="string", length=255, nullable="true")
     */
    private $formaPago;
    
    /**
     * @var text $items
     * 
     * @ORM\Column(name="items", type="text")
     */
    private $items;
    
    /**
     * @var text $mostrarColumnas
     * @Assert\MaxLength(255)
     * @ORM\Column(name="mostrarColumnas", type="string", length=255, nullable="true")
     */
    private $mostrarColumnas;
    
    /**
     * @var text $mostrarFirmas
     * @ORM\Column(name="mostrarFirmas", type="boolean")
     */
    private $mostrarFirmas;
    
    /**
     * @var text $observacion
     *
     * @ORM\Column(name="observacion", type="text", nullable="true")
     */
    private $observacion;
    
    
    /**
     *
     * @var $estado (0-En espera, 1-Aprobado, 2-No Aprobado)
     * @ORM\Column(name="estado", type="integer", nullable="true")
     *
     */
    private $estado;
    
    /**
     * @var date $fechaCreado
     *
     * @ORM\Column(name="fechaCreado", type="date")
     */
    private $fechaCreado;
    
    /**
     * @var date $fechaActualizado
     *
     * @ORM\Column(name="fechaActualizado", type="date")
     */
    private $fechaActualizado;
    
    
    /**
     * @var Pedido $pedido
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="Pedido", inversedBy="presupuestos" )
     */
    private $pedido;

    /**
     *
     * @ORM\OneToMany(targetEntity="Decoyeso\PedidoBundle\Entity\PresupuestoElemento", mappedBy="presupuesto", cascade={"persist","remove"})
     */
    private $presupuestoElemento;
    
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaCreado (new \DateTime);
    	$this->setFechaActualizado (new \DateTime);
    	$this->setEstado(0);
    }
    
    /**
     * @ORM\preUpdate
     */
    public function preUpdate()
    {
    	$this->setFechaActualizado (new \DateTime);
    }
    
    /**
     * @ORM\postPersist
     */
    public function postPersist()
    {
    	$this->numero= "PRES".str_pad($this->getId(),5,0,STR_PAD_LEFT);
    }
    
    /**
     * To String
     *
     * @return string
     */
    public function __toString() 
    {
    	return $this->getNombre()." ".$this->getNumero();
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
     * Set fechaCreado
     *
     * @param date $fechaCreado
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;
    }

    /**
     * Get fechaCreado
     *
     * @return date 
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Set fechaActualizado
     *
     * @param date $fechaActualizado
     */
    public function setFechaActualizado($fechaActualizado)
    {
        $this->fechaActualizado = $fechaActualizado;
    }

    /**
     * Get fechaActualizado
     *
     * @return date 
     */
    public function getFechaActualizado()
    {
        return $this->fechaActualizado;
    }

    /**
     * Set items
     *
     * @param text $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * Get items
     *
     * @return text 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set subTotal
     *
     * @param string $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * Get subTotal
     *
     * @return string 
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * Set total
     *
     * @param string $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * Get total
     *
     * @return string 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set precioEntrega
     *
     * @param string $precioEntrega
     */
    public function setPrecioEntrega($precioEntrega)
    {
        $this->precioEntrega = $precioEntrega;
    }

    /**
     * Get precioEntrega
     *
     * @return string 
     */
    public function getPrecioEntrega()
    {
        return $this->precioEntrega;
    }

    /**
     * Set precioTextoEntrega
     *
     * @param string $precioTextoEntrega
     */
    public function setPrecioTextoEntrega($precioTextoEntrega)
    {
        $this->precioTextoEntrega = $precioTextoEntrega;
    }

    /**
     * Get precioTextoEntrega
     *
     * @return string 
     */
    public function getPrecioTextoEntrega()
    {
        return $this->precioTextoEntrega;
    }

    /**
     * Set precioSaldo
     *
     * @param string $precioSaldo
     */
    public function setPrecioSaldo($precioSaldo)
    {
        $this->precioSaldo = $precioSaldo;
    }

    /**
     * Get precioSaldo
     *
     * @return string 
     */
    public function getPrecioSaldo()
    {
        return $this->precioSaldo;
    }

    /**
     * Set formaPago
     *
     * @param string $formaPago
     */
    public function setFormaPago($formaPago)
    {
        $this->formaPago = $formaPago;
    }

    /**
     * Get formaPago
     *
     * @return string 
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }

    /**
     * Set mostrarColumnas
     *
     * @param integer $mostrarColumnas
     */
    public function setMostrarColumnas($mostrarColumnas)
    {
    	
        $this->mostrarColumnas = json_encode($mostrarColumnas);
    }

    /**
     * Get mostrarColumnas
     *
     * @return integer 
     */
    public function getMostrarColumnas()
    {
        return json_decode($this->mostrarColumnas, true);
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
     * Set mostrarFirmas
     *
     * @param boolean $mostrarFirmas
     */
    public function setMostrarFirmas($mostrarFirmas)
    {
        $this->mostrarFirmas = $mostrarFirmas;
    }

    /**
     * Get mostrarFirmas
     *
     * @return boolean 
     */
    public function getMostrarFirmas()
    {
        return $this->mostrarFirmas;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set pedido
     *
     * @param Decoyeso\PedidoBundle\Entity\Pedido $pedido
     */
    public function setPedido(\Decoyeso\PedidoBundle\Entity\Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    /**
     * Get pedido
     *
     * @return Decoyeso\PedidoBundle\Entity\Pedido 
     */
    public function getPedido()
    {
        return $this->pedido;
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
    
    public function getEstadoNombre(){
    	
    	$estado[0]='En espera';
    	$estado[1]='Aprobado';
    	$estado[2]='No aprobado';
    	if ($this->getEstado() == null)
    		return $estado[0];

    	return $estado[$this->getEstado()];
    }
    
    public function __construct()
    {
        $this->presupuestoElemento = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add presupuestoElemento
     *
     * @param Decoyeso\PedidoBundle\Entity\PresupuestoElemento $presupuestoElemento
     */
    public function addPresupuestoElemento(\Decoyeso\PedidoBundle\Entity\PresupuestoElemento $presupuestoElemento)
    {
        $this->presupuestoElemento[] = $presupuestoElemento;
    }

    /**
     * Get presupuestoElemento
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPresupuestoElemento()
    {
        return $this->presupuestoElemento;
    }
}