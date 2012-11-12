<?php

namespace Decoyeso\ProduccionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\ProduccionBundle\Entity\LugarSecador
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\ProduccionBundle\Entity\LugarSecadorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class LugarSecador
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
     */
    private $nombre;

    /**
     * @var boolean $disponible
     *
     * @ORM\Column(name="disponible", type="boolean")
     */
    private $disponible;
    
    
    /**
     * @var $secador
     *
     * @ORM\ManyToOne(targetEntity="Secador", inversedBy="lugaresSecador")
     */
    private $secador;
    
    
    /**
     * @var $proceso
     *
     * @ORM\ManyToOne(targetEntity="Proceso", inversedBy="lugaresProceso")
     */
    private $proceso;
    
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
     * @var date $fechaAsignado
     *
     * @ORM\Column(name="fechaAsignado", type="datetime", nullable="true")
     */
    private $fechaAsignado;

    
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
    
    public function diasEnSecador() {
    	$hoy = new \DateTime;
    	$fechaAsignado = $this->fechaAsignado;
    	
    	$dif = $hoy->diff($fechaAsignado);
    	return $dif->format("%d");
    }
    //Subsecretaria de juventud, santiago del estero 369. Arias Alberto
    public function calcularEtapaDeSecado($tiempoSecadoSecador) {
    	//Seco = 0
    	if ($this->diasEnSecador() > $tiempoSecadoSecador) return 0;
    	
    	//Semi Seco = 1
    	if ($this->diasEnSecador() > $tiempoSecadoSecador/2) return 1;

    	//Mojado = 2
    	if ($this->diasEnSecador() < $tiempoSecadoSecador/2) return 2;
    	
    	
  
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
     * Set disponible
     *
     * @param boolean $disponible
     */
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;
    }

    /**
     * Get disponible
     *
     * @return boolean 
     */
    public function getDisponible()
    {
        return $this->disponible;
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
     * Set secador
     *
     * @param Decoyeso\ProduccionBundle\Entity\Secador $secador
     */
    public function setSecador(\Decoyeso\ProduccionBundle\Entity\Secador $secador)
    {
        $this->secador = $secador;
    }

    /**
     * Get secador
     *
     * @return Decoyeso\ProduccionBundle\Entity\Secador 
     */
    public function getSecador()
    {
        return $this->secador;
    }

    /**
     * Set fechaAsignado
     *
     * @param datetime $fechaAsignado
     */
    public function setFechaAsignado($fechaAsignado)
    {
        $this->fechaAsignado = $fechaAsignado;
    }

    /**
     * Get fechaAsignado
     *
     * @return datetime 
     */
    public function getFechaAsignado()
    {
        return $this->fechaAsignado;
    }

    /**
     * Set proceso
     *
     * @param Decoyeso\ProduccionBundle\Entity\Proceso $proceso
     */
    public function setProceso(\Decoyeso\ProduccionBundle\Entity\Proceso $proceso)
    {
        $this->proceso = $proceso;
    }

    /**
     * Get proceso
     *
     * @return Decoyeso\ProduccionBundle\Entity\Proceso 
     */
    public function getProceso()
    {
        return $this->proceso;
    }
}