<?php

namespace Decoyeso\ClientesBundle\Entity;

use Decoyeso\UbicacionBundle\Entity\Localidad;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as assert;

/**
 * Decoyeso\ClientesBundle\Entity\Cliente
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\ClientesBundle\Entity\ClienteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cliente
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected  $id;
    
    /**
     * @var string $numero
     *
     * @ORM\Column(name="numero", type="string", length=255,nullable="true")
     */
    protected $numero;
    
    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=255)
	 * @assert\NotBlank(message="Por favor, ingrese el nombre")
     */
    protected $nombre;
    
    /**
     * @var integer $tipo
     *
     * @ORM\Column(name="tipo", type="integer")
     */
    protected  $tipo;
    

    /**
     * @var string $telefono
     *
     * @ORM\Column(name="telefono", type="string", length=50, nullable="true")
     */
    protected $telefono;

    /**
     * @var string $celular
     *
     * @ORM\Column(name="celular", type="string", length=50,nullable="true")
     */
    protected $celular;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255,nullable="true")
     * @assert\Email(message="Por favor, ingrese un e-mail válido")
     */
    protected $email;
    
    
    /**
     * @var provincia
     *
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UbicacionBundle\Entity\Provincia")
     *
     */
    protected $provincia;
    
    /**
     * @var departamento
     *
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UbicacionBundle\Entity\Departamento")
     *
     */
    protected $departamento;
    
    /**
     * @var Localidad
     * 
     * @ORM\ManyToOne(targetEntity="\Decoyeso\UbicacionBundle\Entity\Localidad")
     * 
     */
    protected $localidad;
    
    /**
     * @var string $barrio
     *
     * @ORM\Column(name="barrio", type="string", length=255,nullable="true")
     */
    protected $barrio;
    
    /**
     * @var string $calle
     *
     * @ORM\Column(name="calle", type="string", length=255, nullable="true")
     */
    protected $calle;
    
    /**
     * @var string $numeroCalle
     *
     * @ORM\Column(name="numeroCalle", type="string", length=255, nullable="true")
     */
    protected $numeroCalle;
    
    /**
     * @var string $dni
     *
     * @ORM\Column(name="dni", type="string", length=10, nullable="true")
     */
    protected $dni;

    /**
     * @var string $cuitOcuil
     *
     * @ORM\Column(name="cuitOcuil", type="string", length=50, nullable="true")
     */
    protected $cuitOcuil;
    
    /**
     * @var text $observaciones
     *
     * @ORM\Column(name="observaciones", type="text", nullable="true")
     */
    protected $observaciones;


    /**
     * @var Pedido $pedidos
     *
     * @ORM\OneToMany(targetEntity="Decoyeso\PedidoBundle\Entity\Pedido", mappedBy="cliente", cascade={"remove"})
     */
    protected $pedidos;

    
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
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaCreado (new \DateTime);
    	$this->setFechaActualizado (new \DateTime);
    }
    
    /**
     * @ORM\postPersist
     */
    public function postPersist()
    {
    	$this->numero="CLI".str_pad($this->getId(),5,0,STR_PAD_LEFT);
    }
    
    /**
     * @ORM\preUpdate
     */
    public function preUpdate()
    {
    	$this->setFechaActualizado (new \DateTime);
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
     * Set telefono
     *
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set celular
     *
     * @param string $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * Get celular
     *
     * @return string 
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dni
     *
     * @param string $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    /**
     * Get dni
     *
     * @return string 
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set cuitOcuil
     *
     * @param string $cuitOcuil
     */
    public function setCuitOcuil($cuitOcuil)
    {
        $this->cuitOcuil = $cuitOcuil;
    }

    /**
     * Get cuitOcuil
     *
     * @return string 
     */
    public function getCuitOcuil()
    {
        return $this->cuitOcuil;
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
    
    public function __toString(){
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
     * @var $pedido
     * (Lado Inverso)
     * @ORM\OneToMany(targetEntity="Decoyeso\PedidoBundle\Entity\Pedido", mappedBy="cliente",cascade={"persist","remove"})
     */
    public $pedido;
    

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
     * Set observaciones
     *
     * @param text $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    /**
     * Get observaciones
     *
     * @return text 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }
    public function __construct()
    {
        $this->pedidos = new \Doctrine\Common\Collections\ArrayCollection();

    }
    
    /**
     * Add pedidos
     *
     * @param Decoyeso\PedidoBundle\Entity\Pedido $pedidos
     */
    public function addPedido(\Decoyeso\PedidoBundle\Entity\Pedido $pedidos)
    {
        $this->pedidos[] = $pedidos;
    }

    /**
     * Get pedidos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPedidos()
    {
        return $this->pedidos;
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
     * Get pedido
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPedido()
    {
        return $this->pedido;
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
}