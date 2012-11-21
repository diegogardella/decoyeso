<?php

namespace Decoyeso\ProduccionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;


/**
 * Decoyeso\ProduccionBundle\Entity\Molde
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\ProduccionBundle\Entity\MoldeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @DoctrineAssert\UniqueEntity("producto")

 */
class Molde
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
     * @var string $observacion
     *
     * @ORM\Column(name="observacion", type="text", nullable="true")
     */
    private $observacion;

    /**
     * @var Producto $producto
     * @ORM\OneToOne(targetEntity="\Decoyeso\ProductoBundle\Entity\Producto", inversedBy="molde")
     */
    private $producto;

    /**
     * @var integer $cantidad
     * @Assert\NotBlank(message="Por favor, ingrese la cantidad")
     * @Assert\Type(type="integer", message="Ingrese un número")
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;
    
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
    }
    
    
    public function __toString() 
    {
    	return "MOLDE-".$this->getProducto();
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
     * Set cantidad
     *
     * @param integer $cantidad
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
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
}