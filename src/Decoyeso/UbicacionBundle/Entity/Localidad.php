<?php

namespace Decoyeso\UbicacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\UbicacionBundle\Entity\Localidad
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Localidad
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
     * @var integer $departamento_id
     *
     * @ORM\ManyToOne(targetEntity="Departamento")
     */
    private $departamento;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;


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
     * Set departamento
     *
     * @param Decoyeso\UbicacionBundle\Entity\Departamento $departamento
     */
    public function setDepartamento(\Decoyeso\UbicacionBundle\Entity\Departamento $departamento)
    {
        $this->departamento = $departamento;
    }

    /**
     * Get departamento
     *
     * @return Decoyeso\UbicacionBundle\Entity\Departamento 
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }
    
    
    public function __toString(){
    	return $this->nombre;
    }
    
}