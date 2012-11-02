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
	
	
}