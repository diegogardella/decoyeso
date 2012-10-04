<?php

namespace Decoyeso\UsuarioBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Decoyeso\UsuarioBundle\Entity\Usuario
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\UsuarioBundle\Entity\UsuarioRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 * @UniqueEntity("username")

 */
class Usuario extends BaseUser
{
	
	
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $nombre
     * @Assert\NotBlank(message="Por favor ingrese su nombre.")
     * @Assert\MaxLength(limit="255", message="El nombre es demasiado largo.")
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    
    /**
     * @var string $apellido
     * @Assert\MaxLength(limit="255", message="El nombre es demasiado largo.")
     * @ORM\Column(name="apellido", type="string", length=255, nullable="true")
     */
    private $apellido;
    
    /**
     * @var string $telefono
     * @Assert\MaxLength(limit="255", message="El nombre es demasiado largo.")
     * @ORM\Column(name="telefono", type="string", length=255, nullable="true")
     */
    private $telefono;
    
    /**
     * @var string $celular
     * @Assert\MaxLength(limit="255", message="El nombre es demasiado largo.")
     * @ORM\Column(name="celular", type="string", length=255, nullable="true")
     */
    private $celular;

    
    /**
     * @var string $permisos
     * @ORM\Column(name="permisos", type="string", length=255)
     */
    private $permisos;

    
    /**
     * @var string $email
     * @Assert\NotBlank(message="Por favor ingrese el Email.")
     * @Assert\Email(message="Por favor ingrese un Email válido.")
     * @Assert\MaxLength(limit="255", message="El email es demasiado largo.")
     */
    protected $email;    
    
    /**
     * @var string $username
     * @Assert\NotBlank(message="Por favor ingrese el nombre de usuario.")
     * @Assert\MaxLength(limit="255", message="El nombre de usuario es demasiado largo.")
     */
    protected $username;
    
    /**
     * @var string $plainPassword
     * @Assert\MaxLength(limit="255", message="El nombre es demasiado largo.")
     */
    protected $plainPassword;
    

    
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
     * @var $logs
     * (Lado Inverso)
     * @ORM\OneToMany(targetEntity="Decoyeso\UsuarioBundle\Entity\Usuario", mappedBy="usuario", orphanRemoval=true)
     */
    private $logs;
    
    
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
    

    public function __construct()
    {
    	parent::__construct();
    
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
     * Set apellido
     *
     * @param string $apellido
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
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
     * Add logs
     *
     * @param Decoyeso\UsuarioBundle\Entity\Usuario $logs
     */
    public function addUsuario(\Decoyeso\UsuarioBundle\Entity\Usuario $logs)
    {
        $this->logs[] = $logs;
    }

    /**
     * Get logs
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getLogs()
    {
        return $this->logs;
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
     * Set permisos
     *
     * @param string $permisos
     */
    public function setPermisos($permisos)
    {
        $this->permisos = $permisos;
    }

    /**
     * Get permisos
     *
     * @return string 
     */
    public function getPermisos()
    {
        return $this->permisos;
    }
}
