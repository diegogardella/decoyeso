<?php

namespace Decoyeso\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;

/**
 * Decoyeso\PedidoBundle\Entity\Relevamiento
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\PedidoBundle\Entity\RelevamientoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Relevamiento
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
     * @ORM\Column(name="numero", type="string", length=12, nullable="true")
     */
    private $numero;

    /**
     * @var text $descripcion
     *
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;
    
    /**
     * @var text $descripcion
     *
     * @ORM\Column(name="descripcion", type="text")
	 * @assert\NotBlank(message="Por favor, ingrese descripción")
     */
    private $descripcion;    
    

    
    /**
     * @var date $fechaCreado
     *
     * @ORM\Column(name="fechaCreado", type="date")
     */
    protected $fechaCreado;
    
    /**
     * @var date $fechaActualizado
     *
     * @ORM\Column(name="fechaActualizado", type="date")
     */
    protected $fechaActualizado;
    
    
    /**
     * @var Pedido $pedido
     *
     * @ORM\ManyToOne(targetEntity="Pedido", inversedBy="relevamientos")
	 * @assert\NotBlank(message="Por favor, seleccione pedido")
     */
    protected $pedido;
    
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaCreado (new \DateTime);
    	$this->setFechaActualizado (new \DateTime);
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
    	$this->numero="REL".str_pad($this->getId(),5,0,STR_PAD_LEFT);
    }
    
    public function __toString() 
    {
    	return $this->getNumero()."-".$this->getNombre();
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
     * Set descripcion
     *
     * @param text $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * Get descripcion
     *
     * @return text 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
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
     * Set nombre
     *
     * @param stringt $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return stringt 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
}