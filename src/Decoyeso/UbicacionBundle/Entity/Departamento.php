<?php

namespace Decoyeso\UbicacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\UbicacionBundle\Entity\Departamento
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Departamento
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
     * @var integer $provincia_id
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     */
    private $provincia;
    

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
     * Set provincia
     *
     * @param Decoyeso\UbicacionBundle\Entity\Provincia $provincia
     */
    public function setProvincia(\Decoyeso\UbicacionBundle\Entity\Provincia $provincia)
    {
        $this->provincia = $provincia;
    }

    /**
     * Get provincia
     *
     * @return Decoyeso\UbicacionBundle\Entity\Provincia 
     */
    public function getProvincia()
    {
        return $this->provincia;
    }
    
    public function __toString(){
    	return $this->nombre;
    }
}