<?php

namespace Decoyeso\PedidoBundle\Entity;

use Decoyeso\UbicacionBundle\Entity\Provincia;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;

/**
 * Pedido\PedidoBundle\Entity\Pedido
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Pedido
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
     * @var integer $id
     *
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;

    /**
     * @var $cliente
     *
     * @ORM\ManyToOne(targetEntity="Decoyeso\ClientesBundle\Entity\Cliente", inversedBy="pedidos")
	 * @assert\NotBlank(message="Por favor, seleccione un cliente")
     */
    private $cliente;
    
    /**
     * @ORM\Column(name="requiereRelevamiento", type="boolean")
     */
    private $requiereRelevamiento;
    
    /**
     * @var $estado
     * @ORM\Column(name="estado", type="integer", nullable="true")
     * 
     */
    private $estado;

    /**
     * var $prioridad;
     * @ORM\Column(name="prioridad",type="integer")
     */
    private $prioridad;
    

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length="255")
     */
    private $nombre;

    /**
     * @var text $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable="true")
     */
    private $descripcion;


    
    /**
     * @var date $fechaSolicitado
     *
     * @ORM\Column(name="fechaSolicitado", type="date")
     */
    private $fechaSolicitado;
    
    /**
     * @var date $fechaEntrega
     *
     * @ORM\Column(name="fechaEntrega", type="date")
     */
    private $fechaEntrega;

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
     * @var Relevamiento $relevamientos
     *
     * @ORM\OneToMany(targetEntity="Relevamiento", mappedBy="pedido", cascade={"remove"}))
     */
    private $relevamientos;
    
    /**
     * @var Presupuesto $presupuestos
     *
     * @ORM\OneToMany(targetEntity="Presupuesto", mappedBy="pedido", cascade={"remove"}))
     */
    private $presupuestos;
    

    
    /**
     * @var provincia
     * 
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UbicacionBundle\Entity\Provincia") 
     */
    
    private $provincia;
    
    /**
     * @var departamento
     *
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UbicacionBundle\Entity\Departamento")
     *
     */
    private $departamento;    
    

    /**
     * @var localidad
     *
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UbicacionBundle\Entity\Localidad")
     *
     */
    private $localidad;    
    
    
    /**
     * @var string $barrio
     *
     * @ORM\Column(name="barrio", type="string", length=255, nullable="true")
     */
    private $barrio;
    
    /**
     * @var string $calle
     *
     * @ORM\Column(name="calle", type="string", length=255, nullable="true")
     */
    private $calle;
    
    /**
     * @var string $numeroCalle
     *
     * @ORM\Column(name="numeroCalle", type="string", length=255, nullable="true")
     */
    private $numeroCalle;
    
    /**
     * @var integer $solicitudMovimientoElemento
     *
     * @ORM\OneToMany(targetEntity="Decoyeso\StockBundle\Entity\SolicitudMovimiento",mappedBy="pedido")
     */
    private $solicitudMovimiento;
    
    
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaCreado (new \DateTime);
    	$this->setFechaActualizado (new \DateTime);
    	$this->estado=1;
    }
    
    /**
     * @ORM\postPersist
     */
    public function postPersist()
    {
    	$this->numero="PED".str_pad($this->getId(),5,0,STR_PAD_LEFT);
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
    	return $this->numero."-".$this->nombre;
    }

 
    public function getRequiereRelevamiento(){
    	return $this->requiereRelevamiento;
    }
    
    public function setRequiereRelevamiento($requiereRelevamiento){
    	$this->requiereRelevamiento=$requiereRelevamiento;
    }
    
    public function getRequiereRelevamientoNombre(){
    	
    	switch ($this->getRequiereRelevamiento()){
    		
    		case 1:
    			return "No";
    		break;
    		
    		case 2:
    			return "Si";
    		break;
    	}
    	
    }

    
    //Devuelve el nombre del estado
    
    public function getEstadoNombre(){
    	
    	switch ($this->getEstado()){
    		case 1:
    			return "Pedido Creado";
    		break;
    		
    		case 2:
    			return "Relevamiento creado";
    		break;

    		case 3:
    			return "Presupuesto creado";
    			
    		case 4:
    			return "Presupuesto Aprobado";
    		break;
    		
    		case ($this->getEstado()==5 or $this->getEstado()==6):
    			if($this->getTipo()==2){
    				return "Obra en Ejecución";
    			}else{
    				if($this->getEstado()==5){
    					return "Solicitud a Stock Enviada ";
    				}else{
    					return "Solicitud a Stock Procesada".$this->getEstado();
    				}
    			}
    			
    		break;
    		
    		case 7:
    			return "Pedido Finalizado";
    		break;
    		
    	}
    }
    
    public function verificarEstado(){
    	
    	$bEstados=1; 
    	
    	if(count($this->getRelevamientos())>0){
    		$bEstados=2;
    		$this->requiereRelevamiento=1;
    	}
    	    	
    	if(count($this->getPresupuestos())>0){
    		$bEstados=3;
    	}
    	
    	
    	foreach ($this->getPresupuestos() as $presupuesto){
    		if($presupuesto->getEstado()==1){
    			$bEstados=4;
    		}	
    	}
    	
    	$this->estado=$bEstados;
    }
    
   
    
    
    public function getPrioridadNombre(){
    
    	switch ($this->getPrioridad()){
    		case 1:
    			return "Baja";
    			break;
    
    		case 2:
    			return "Media";
    		break;
    		
    		case 3:
    			return "Alta";
    		break;
    		
    		case 4:
    			return "Muy alta";
    		break;
    	}
    }

  
    public function __construct()
    {
        $this->relevamientos = new \Doctrine\Common\Collections\ArrayCollection();
    $this->presupuestos = new \Doctrine\Common\Collections\ArrayCollection();
    $this->obra = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set prioridad
     *
     * @param integer $prioridad
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;
    }

    /**
     * Get prioridad
     *
     * @return integer 
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set nombre
     *
     * @param strinombre
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
     * Set fechaSolicitado
     *
     * @param date $fechaSolicitado
     */
    public function setFechaSolicitado($fechaSolicitado)
    {
        $this->fechaSolicitado = $fechaSolicitado;
    }

    /**
     * Get fechaSolicitado
     *
     * @return date 
     */
    public function getFechaSolicitado()
    {
        return $this->fechaSolicitado;
    }

    /**
     * Set fechaEntrega
     *
     * @param date $fechaEntrega
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;
    }

    /**
     * Get fechaEntrega
     *
     * @return date 
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
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
     * Set barrio
     *
     * @param string $barrio
     */
    public function setBarrio($barrio)
    {
        $this->barrio = $barrio;
    }

    /**
     * Get barrio
     *
     * @return string 
     */
    public function getBarrio()
    {
        return $this->barrio;
    }

    /**
     * Set calle
     *
     * @param string $calle
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;
    }

    /**
     * Get calle
     *
     * @return string 
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set numeroCalle
     *
     * @param string $numeroCalle
     */
    public function setNumeroCalle($numeroCalle)
    {
        $this->numeroCalle = $numeroCalle;
    }

    /**
     * Get numeroCalle
     *
     * @return string 
     */
    public function getNumeroCalle()
    {
        return $this->numeroCalle;
    }

    /**
     * Set cliente
     *
     * @param Decoyeso\ClientesBundle\Entity\Cliente $cliente
     */
    public function setCliente(\Decoyeso\ClientesBundle\Entity\Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Get cliente
     *
     * @return Decoyeso\ClientesBundle\Entity\Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Add relevamientos
     *
     * @param Decoyeso\PedidoBundle\Entity\Relevamiento $relevamientos
     */
    public function addRelevamiento(\Decoyeso\PedidoBundle\Entity\Relevamiento $relevamientos)
    {
        $this->relevamientos[] = $relevamientos;
    }

    /**
     * Get relevamientos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getRelevamientos()
    {
        return $this->relevamientos;
    }

    /**
     * Add presupuestos
     *
     * @param Decoyeso\PedidoBundle\Entity\Presupuesto $presupuestos
     */
    public function addPresupuesto(\Decoyeso\PedidoBundle\Entity\Presupuesto $presupuestos)
    {
        $this->presupuestos[] = $presupuestos;
    }

    /**
     * Get presupuestos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPresupuestos()
    {
        return $this->presupuestos;
    }

    /**
     * Add obra
     *
     * @param Decoyeso\ObraBundle\Entity\Obra $obra
     */
    public function addObra(\Decoyeso\ObraBundle\Entity\Obra $obra)
    {
        $this->obra[] = $obra;
    }

    /**
     * Get obra
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getObra()
    {
        return $this->obra;
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

    /**
     * Set localidad
     *
     * @param Decoyeso\UbicacionBundle\Entity\Localidad $localidad
     */
    public function setLocalidad(\Decoyeso\UbicacionBundle\Entity\Localidad $localidad)
    {
        $this->localidad = $localidad;
    }

    /**
     * Get localidad
     *
     * @return Decoyeso\UbicacionBundle\Entity\Localidad 
     */
    public function getLocalidad()
    {
        return $this->localidad;
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
     * Add solicitudMovimiento
     *
     * @param Decoyeso\StockBundle\Entity\SolicitudMovimiento $solicitudMovimiento
     */
    public function addPedido(\Decoyeso\StockBundle\Entity\SolicitudMovimiento $solicitudMovimiento)
    {
        $this->solicitudMovimiento[] = $solicitudMovimiento;
    }

    /**
     * Get solicitudMovimiento
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSolicitudMovimiento()
    {
        return $this->solicitudMovimiento;
    }
}