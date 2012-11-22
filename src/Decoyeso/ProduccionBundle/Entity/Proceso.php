<?php

namespace Decoyeso\ProduccionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\ProduccionBundle\Entity\Proceso
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\ProduccionBundle\Entity\ProcesoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Proceso
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
     * @var integer $numero
     *
     * @ORM\Column(name="numero", type="string",length="12",nullable="true")
     */
    private $numero;
    
    /**
     *
     * @var $estado
     * 
     * @ORM\Column(name="estado", type="integer", nullable="true")
     *
     */
    private $estado;
    
    /**
     * @var date $fechaCreado
     *
     * @ORM\Column(name="fechaCreado", type="date")
     */
    private $fechaCreado;
    
    /**
     * @var date $fechaInicio
     *
     * @ORM\Column(name="fechaInicio", type="date", nullable="true")
     */
    private $fechaInicio;
    
    
    /**
     * @var date $datosInsumos
     *
     * @ORM\Column(name="datosInsumos", type="text", nullable="true")
     */
    
    
    private $datosInsumos;
     
    /**
     * @var date $fechaFin
     *
     * @ORM\Column(name="fechaFin", type="date", nullable="true")
     */
    private $fechaFin;
    
    /**
     * @var lugaresSecador
     *
     * @ORM\OneToMany(targetEntity="LugarSecador", mappedBy="proceso")
     *
     */
    private $lugaresSecador;
    
    /**
     * @var date $fechaActualizado
     *
     * @ORM\Column(name="fechaActualizado", type="date")
     */
    private $fechaActualizado;
       
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaCreado (new \DateTime);
    	$this->setFechaActualizado (new \DateTime);
    	$this->setEstado(0);
    }
    
    /**
     * @ORM\preUpdate
     */
    public function preUpdate()
    {
    	$this->setFechaActualizado (new \DateTime);
    }
    
    /**
     * @ORM\postPersist
     */
    public function postPersist()
    {
    	$this->numero= "PROC".str_pad($this->getId(),5,0,STR_PAD_LEFT);
    }
    
    /**
     * To String
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->getNumero();
    }
    
    public function getNombreEstado () {
    	//si cambiar acá, tmb se cambia en el form
    	$nombreEstado[0] = "No Iniciado";
    	$nombreEstado[1] = "Iniciado";
    	$nombreEstado[2] = "Finalizado";
    	$nombreEstado[3] = "Finalizado y productos asignados";
    	
    	return $nombreEstado[$this->estado];
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
     * Set numero
     *
     * @param string $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Get estado
     *
     * @return integer 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set fechaInicio
     *
     * @param date $fechaInicio
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * Get fechaInicio
     *
     * @return date 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
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
     * Set fechaFin
     *
     * @param date $fechaFin
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;
    }

    /**
     * Get fechaFin
     *
     * @return date 
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
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

    /**
     * Set datosInsumos
     *
     * @param text $datosInsumos
     */
    public function setDatosInsumos($datosInsumos)
    {
        $this->datosInsumos = $datosInsumos;
    }

    /**
     * Get datosInsumos
     *
     * @return text 
     */
    public function getDatosInsumos()
    {
        return json_decode($this->datosInsumos, true);
    }
}