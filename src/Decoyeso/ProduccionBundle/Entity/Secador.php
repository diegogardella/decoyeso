<?php

namespace Decoyeso\ProduccionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Decoyeso\ProduccionBundle\Entity\Secador
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\ProduccionBundle\Entity\SecadorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Secador
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
     * @Assert\NotBlank(message="Por favor, escriba una nombre")
     * @Assert\MaxLength(255)
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    
    /**
     * @var integer $tipo
     * 0 = placa, 1 = moldura
     * @ORM\Column(name="tipo", type="integer")
     */
    private  $tipo;
    
    /**
     * @var integer $tiempoSecado
     * @Assert\NotBlank(message="Por favor, ingrese el tiempo de secado")
     * @Assert\Type(type="integer", message="Ingrese un número")
     * @ORM\Column(name="tiempoSecado", type="integer")
     */
    private  $tiempoSecado;

    /**
     * @var integer $capacidad
     * @Assert\NotBlank(message="Por favor, ingrese la capacidad")
     * @Assert\Type(type="integer", message="Ingrese un número")
     * @ORM\Column(name="capacidad", type="integer")
     */
    private $capacidad;

    /**
     * @var text $observacion
     *
     * @ORM\Column(name="observacion", type="text", nullable="true")
     */
    private $observacion;
    
    
    /**
     * @var lugaresSecador
     *
     * @ORM\OneToMany(targetEntity="LugarSecador", mappedBy="secador", cascade={"remove"})
     *
     */
    private $lugaresSecador;
    
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
    
    
    public function getNombreTipo()
    {
    	$nombreTipo[0] = "Placa";
    	$nombreTipo[1] = "Moldura";
    	return $nombreTipo[$this->tipo];
    }
    
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
        
    
    public function __toString()
    {
    	return $this->nombre;
    }
    
    public function setLugarSecador() {
    	$this->lugaresSecador = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set capacidad
     *
     * @param integer $capacidad
     */
    public function setCapacidad($capacidad)
    {
        $this->capacidad = $capacidad;
    }

    /**
     * Get capacidad
     *
     * @return integer 
     */
    public function getCapacidad()
    {
        return $this->capacidad;
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
     * Set tiempoSecado
     *
     * @param integer $tiempoSecado
     */
    public function setTiempoSecado($tiempoSecado)
    {
        $this->tiempoSecado = $tiempoSecado;
    }

    /**
     * Get tiempoSecado
     *
     * @return integer 
     */
    public function getTiempoSecado()
    {
        return $this->tiempoSecado;
    }
    public function __construct()
    {
        $this->lugaresSecador = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    
    /**
     * Add lugaresSecador
     *
     * @param Decoyeso\ProduccionBundle\Entity\LugarSecador $lugaresSecador
     */
    public function addLugarSecador(\Decoyeso\ProduccionBundle\Entity\LugarSecador $lugaresSecador)
    {
        $this->lugaresSecador[] = $lugaresSecador;
    }

    /**
     * Get lugaresSecador
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getLugaresSecador()
    {
        return $this->lugaresSecador;
    }
}