<?php

namespace Decoyeso\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;

/**
 * Decoyeso\StockBundle\Entity\MovimientoStock
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\StockBundle\Entity\MovimientoStockRepository")
 */
class MovimientoStock
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
     * @var integer $producto
     *
     * @ORM\ManyToOne(targetEntity="Decoyeso\ProductoBundle\Entity\Elemento" , inversedBy="movimientoStock")
     */
    private $elemento;

    /**
     * @var integer $accion
     *
     * @ORM\Column(name="accion", type="integer",nullable=true)
     */
    private $accion;
    
    /**
     * @var integer $motivo-->1-nuevo,2-Se retira en respuesta de solicitud  3-Reingreso, 4-Mal Estado, 6-Rotura Accidental, 8-otros, 10- Se retira para produccion
     *
     * @ORM\Column(name="motivo", type="integer")
     */
    private $motivo;


    /**
     * @var text $observacion
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    private $observacion;

    /**
     * @var float $cantidad
     *
     * @ORM\Column(name="cantidad", type="float")
     * @assert\NotBlank(message="Por favor, ingrese cantidad") 
	 * @assert\Type(type="integer", message="EL valor de precio solo puede ser un número entero o decimal")
     */
    private $cantidad;

    /**
     * @var datetime $fechaHora
     *
     * @ORM\Column(name="fechaHora", type="datetime")
     */
    private $fechaHora;

    /**
     * @var integer $usuario
     *
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UsuarioBundle\Entity\Usuario")
     */
    private $usuario;


    
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaHora (new \DateTime);
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
     * Set accion
     *
     * @param integer $accion
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
    }

    /**
     * Get accion
     *
     * @return integer 
     */
    public function getAccion()
    {
        return $this->accion;
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
     * Set fechaHora
     *
     * @param datetime $fechaHora
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;
    }

    /**
     * Get fechaHora
     *
     * @return datetime 
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * Set usuario
     *
     * @param integer $usuario
     */
    public function setUsuario(\Decoyeso\UsuarioBundle\Entity\Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Get usuario
     *
     * @return integer 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }



    /**
     * Set motivo
     *
     * @param integer $motivo
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    /**
     * Get motivo
     *
     * @return integer 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }
}