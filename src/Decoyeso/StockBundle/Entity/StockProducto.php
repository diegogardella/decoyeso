<?php

namespace Decoyeso\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\StockBundle\Entity\StockProducto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\StockBundle\Entity\StockProductoRepository")
 */
class StockProducto
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
     * @ORM\ManyToOne(targetEntity="Decoyeso\ProductoBundle\Entity\Producto Producto")
     */
    private $producto;

    /**
     * @var integer $accion
     *
     * @ORM\Column(name="accion", type="integer")
     */
    private $accion;

    /**
     * @var integer $motivo
     *
     * @ORM\Column(name="motivo", type="integer")
     */
    private $motivo;

    /**
     * @var text $observacion
     *
     * @ORM\Column(name="observacion", type="text")
     */
    private $observacion;

    /**
     * @var float $cantidad
     *
     * @ORM\Column(name="cantidad", type="float")
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
     * @var float $cantidadTotal
     *
     * @ORM\Column(name="cantidadTotal", type="float")
     */
    private $cantidadTotal;


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
     * Set producto
     *
     * @param integer $producto
     */
    public function setProducto(\Decoyeso\ProductoBundle\Entity\Producto $producto)
    {
        $this->producto = $producto;
    }

    /**
     * Get producto
     *
     * @return integer 
     */
    public function getProducto()
    {
        return $this->producto;
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
     * Set cantidadTotal
     *
     * @param float $cantidadTotal
     */
    public function setCantidadTotal($cantidadTotal)
    {
        $this->cantidadTotal = $cantidadTotal;
    }

    /**
     * Get cantidadTotal
     *
     * @return float 
     */
    public function getCantidadTotal()
    {
        return $this->cantidadTotal;
    }
}