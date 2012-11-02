<?php

namespace Decoyeso\ObraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;

/**
 * Decoyeso\ObraBundle\Entity\Obra
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\ObraBundle\Entity\ObraRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Obra
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
     *
     * @var $estado
     * 
     * @ORM\Column(name="estado", type="integer", nullable="true")
     *
     */
    private $estado;
    
    /**
     * @var Pedido $pedido
     * @Assert\NotNull
     * @ORM\OneToOne(targetEntity="Decoyeso\PedidoBundle\Entity\Pedido", inversedBy="obra" )
     */
    private $pedido;
    
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
    	$this->numero= "OBRA".str_pad($this->getId(),5,0,STR_PAD_LEFT);
    }
    
    /**
     * To String
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->getPedido()->getNombreObra()." ".$this->getNumero();
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
    
    /**
     * Get nombreEstado
     *
     * @return string
     */
    public function getNombreEstado()
    {
    	//si cambiar acá, tmb se cambia en el form
    	$nombreEstado[0] = "No Iniciada";
    	$nombreEstado[1] = "En Ejecución";
    	$nombreEstado[2] = "Finalizada";
    	
    	return $nombreEstado[$this->estado];
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
}