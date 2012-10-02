<?php

namespace Decoyeso\LogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decoyeso\LogBundle\Entity\Log
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Decoyeso\LogBundle\Entity\LogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Log
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
     * @var string $entidad
     *
     * @ORM\Column(name="entidad", type="string", length=255)
     */
    private $entidad;
    
    
    /**
     * @var string $log
     *
     * @ORM\Column(name="log", type="string", length=255)
     */
    private $log;
    
    
    /**
     * @var integer $idEntidad
     *
     * @ORM\Column(name="idEntidad", type="integer")
     */
    private $idEntidad;
    

    /**
     * @var datetime $fechaCreado
     *
     * @ORM\Column(name="fechaCreado", type="date")
     */
    private $fechaCreado;
    
    /**
     * @var datetime $fechaCreado
     *
     * @ORM\Column(name="fechaHoraCreado", type="datetime")
     */
    private $fechaHoraCreado;

    
    /**
     * @var $usuario
     * (Lado Propietario)
     * @ORM\ManyToOne(targetEntity="Decoyeso\UsuarioBundle\Entity\Usuario", inversedBy="logs" )
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id", nullable="true")
     */
    public $usuario;
    

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
     * Set log
     *
     * @param string $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Get log
     *
     * @return string 
     */
    public function getLog()
    {
        return $this->log;
    }

    
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaCreado (new \DateTime());
    	$this->setFechaHoraCreado (new \DateTime);

    }

    
    private $doctrine;
    private $securityContext;
    
    public function __construct($doctrine,$securityContext)
    {
    	$this->doctrine = $doctrine;
    	$this->securityContext = $securityContext;
    }
    
    protected $obj;
    
 	public function create($entity, $msj)
     {
     	
     	//$this->obj = $entity;
     	//echo "si anduvo";
     	
     	//exit();
     	$user = $this->securityContext->getToken()->getUser();

     	$this->setUsuario($user);
     	$this->setEntidad(get_class($entity));
     	$this->setIdEntidad($entity->getId());
     	$this->setLog($msj);
     	
     	$em = $this->doctrine->getEntityManager();
     	$em->persist($this);
     	$em->flush();
    
     }
    
    

    /**
     * Set usuario
     *
     * @param Decoyeso\UsuarioBundle\Entity\Usuario $usuario
     */
    public function setUsuario(\Decoyeso\UsuarioBundle\Entity\Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Get usuario
     *
     * @return Decoyeso\UsuarioBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set entidad
     *
     * @param string $entidad
     */
    public function setEntidad($entidad)
    {
        $this->entidad = $entidad;
    }

    /**
     * Get entidad
     *
     * @return string 
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set idEntidad
     *
     * @param integer $idEntidad
     */
    public function setIdEntidad($idEntidad)
    {
        $this->idEntidad = $idEntidad;
    }

    /**
     * Get idEntidad
     *
     * @return integer 
     */
    public function getIdEntidad()
    {
        return $this->idEntidad;
    }

    /**
     * Set fechaCreado
     *
     * @param datetime $fechaCreado
     */
    public function setFechaCreado($fechaCreado)
    {
        $this->fechaCreado = $fechaCreado;
    }

    /**
     * Get fechaCreado
     *
     * @return datetime 
     */
    public function getFechaCreado()
    {
        return $this->fechaCreado;
    }

    /**
     * Set fechaHoraCreado
     *
     * @param datetime $fechaHoraCreado
     */
    public function setFechaHoraCreado($fechaHoraCreado)
    {
        $this->fechaHoraCreado = $fechaHoraCreado;
    }

    /**
     * Get fechaHoraCreado
     *
     * @return datetime 
     */
    public function getFechaHoraCreado()
    {
        return $this->fechaHoraCreado;
    }
}