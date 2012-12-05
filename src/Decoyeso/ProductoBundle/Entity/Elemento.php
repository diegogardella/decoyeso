<?php

namespace Decoyeso\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;


/**
 * Decoyeso\ProductoBundle\Entity\Elemento 
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminador", type="string")
 * @ORM\DiscriminatorMap({"producto" = "Producto", "insumo" = "Insumo", "servicio" = "Servicio"})
 */


class Elemento
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
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     * @assert\NotBlank(message="Por favor, ingrese nombre") 
     */
    private $nombre;

    /**
     * @var text $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string $unidad
     *
     * @ORM\Column(name="unidad", type="string", length=100)
     * @assert\NotBlank(message="Por favor, ingrese unidad") 
     */
    private $unidad;

    /**
     * @var float $precio
     *
     * @ORM\Column(name="precio", type="float")
     * @assert\NotBlank(message="Por favor, ingrese precio")
	 * @assert\Type(type="numeric", message="EL valor de precio solo puede ser un número entero o decimal")
     */
    private $precio;
    
    
    /**
     * @var float $costo
     *
     * @ORM\Column(name="costo", type="float")
     * @assert\NotBlank(message="Por favor, ingrese precio de costo")
     * @assert\Type(type="numeric", message="EL valor de costo solo puede ser un número entero o decimal")
     */
    private $costo;
    

    /**
     *
     * @ORM\OneToMany(targetEntity="Decoyeso\PedidoBundle\Entity\PresupuestoElemento", mappedBy="elemento")
     */
    private $presupuestoElemento;
    

    

    public function __toString(){
    	return $this->getNombre();
    }
       
        
    public function getCantidadPresupuestadaAprobada(){
    	
    	$cantidad=0;
    	foreach($this->getPresupuestoElemento() as $presupuestoElemento){
    		
    		if($presupuestoElemento->getPresupuesto()->getEstado()==1 and $presupuestoElemento->getTipo()==1){
    			
    			$cantidad=$cantidad+$presupuestoElemento->getCantidad();
    		}    		
    	}
    	return $cantidad;
    }
    
    public function getCantidadPresupuestadaIndirectamenteAprobada(){
    	 
    	$cantidad=0;
    	foreach($this->getPresupuestoElemento() as $presupuestoElemento){
    		if($presupuestoElemento->getPresupuesto()->getEstado()==1 and $presupuestoElemento->getTipo()==2){
    			$cantidad=$cantidad+$presupuestoElemento->getCantidad();
    		}
    	}
    	return $cantidad;
    }
    
    
    public function __construct()
    {
        $this->presupuestoElemento = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set unidad
     *
     * @param string $unidad
     */
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;
    }

    /**
     * Get unidad
     *
     * @return string 
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set precio
     *
     * @param float $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    /**
     * Get precio
     *
     * @return float 
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set costo
     *
     * @param float $costo
     */
    public function setCosto($costo)
    {
        $this->costo = $costo;
    }

    /**
     * Get costo
     *
     * @return float 
     */
    public function getCosto()
    {
        return $this->costo;
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