<?php

namespace Decoyeso\ProductoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;

/**
 * Decoyeso\ProductoBundle\Entity\Producto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Producto extends Elemento
{

	/**
	 * @var integer $tipo
	 *
	 * @ORM\Column(name="tipo", type="integer")
	 */
	private $tipo;
	
	/**
	 *
	 * @ORM\OneToMany(targetEntity="ProductoInsumo", mappedBy="producto")
	 */
	private $productoInsumo;
	
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
	 * Set nombre
	 *
	 * @param string $nombre
	 */
	public function setTipo($tipo)
	{
		$this->tipo = $tipo;
	}
	

    public function __construct()
    {
        $this->productoInsumo = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add productoInsumo
     *
     * @param Decoyeso\ProductoBundle\Entity\ProductoInsumo $productoInsumo
     */
    public function addProductoInsumo(\Decoyeso\ProductoBundle\Entity\ProductoInsumo $productoInsumo)
    {
        $this->productoInsumo[] = $productoInsumo;
    }

    /**
     * Get productoInsumo
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductoInsumo()
    {
        return $this->productoInsumo;
    }
}