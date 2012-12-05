<?php

namespace Decoyeso\PedidoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PresupuestoElemento
{
	
	/**
	 *@ORM\Id
	 *@ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
    /**
     * @ORM\ManyToOne(targetEntity="Presupuesto", inversedBy="presupuestoElemento")
     */
    private $presupuesto;

    /**
     * @ORM\ManyToOne(targetEntity="Decoyeso\ProductoBundle\Entity\Elemento", inversedBy="presupuestoElemento")
     */
    private $elemento;

    /**
     * 1=explicito,2=implicito (que se agrega a un presupuesto por componer al servicio)
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;
    
    
    /**
     * @ORM\Column(name="cantidad", type="float")
     */
    private $cantidad;

	

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
     * Set tipo
     *
     * @param integer $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
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
     * Set presupuesto
     *
     * @param Decoyeso\PedidoBundle\Entity\Presupuesto $presupuesto
     */
    public function setPresupuesto(\Decoyeso\PedidoBundle\Entity\Presupuesto $presupuesto)
    {
        $this->presupuesto = $presupuesto;
    }

    /**
     * Get presupuesto
     *
     * @return Decoyeso\PedidoBundle\Entity\Presupuesto 
     */
    public function getPresupuesto()
    {
        return $this->presupuesto;
    }

    /**
     * Set elemento
     *
     * @param Decoyeso\ProductoBundle\Entity\Elemento $elemento
     */
    public function setElemento(\Decoyeso\ProductoBundle\Entity\Elemento $elemento)
    {
        $this->elemento = $elemento;
    }

    /**
     * Get elemento
     *
     * @return Decoyeso\ProductoBundle\Entity\Elemento 
     */
    public function getElemento()
    {
        return $this->elemento;
    }
}