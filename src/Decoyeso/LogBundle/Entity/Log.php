<?php

namespace Decoyeso\LogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(name="entidad", type="string", length=255, nullable="true")
     */
    private $entidad;
     
    /**
     * @var string $log
     * @Assert\NotBlank(message="Por favor, ingrese un texto")
     * @ORM\Column(name="log", type="text")
     */
    private $log;
    
    /**
     * @var integer $idEntidad
     *
     * @ORM\Column(name="idEntidad", type="integer", nullable="true")
     */
    private $idEntidad;
    
    
    /**
     * @var string $permisos
     * @ORM\Column(name="permisos", type="string", length=255, nullable="true")
     */
    private $permisos;
    
    /**
     * @var integer $prioridad
     * 0 = blanca, 1 = verde, 2 = amarillo, 3 = rojo
     * @ORM\Column(name="prioridad", type="integer", nullable="true")
     */
    private $prioridad;
    
    
    

    /**
     * @var datetime $fechaCreado
     *
     * @ORM\Column(name="fechaCreado", type="date")
     */
    private $fechaCreado;
    
    /**
     * @var datetime $fechaHoraCreado
     *
     * @ORM\Column(name="fechaHoraCreado", type="datetime")
     */
    private $fechaHoraCreado;

    
    /**
     * @var $usuario
     * (Lado Propietario)
     * @ORM\ManyToOne(targetEntity="Decoyeso\UsuarioBundle\Entity\Usuario", inversedBy="logs" )
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;
    
    private $doctrine;
    
    private $securityContext;
    
    
    /**
     * @ORM\prePersist
     */
    public function prePersist()
    {
    	$this->setFechaCreado (new \DateTime());
    	$this->setFechaHoraCreado (new \DateTime);

    }
   
    public function __construct($doctrine,$securityContext)
    {
    	$this->doctrine = $doctrine;
    	$this->securityContext = $securityContext;
    }
    

    public function __toString()
    {
    	return $this->log;
    }
    


    
 	public function create($entity, $msj)
     {

     	$user = $this->securityContext->getToken()->getUser();
     	$this->setUsuario($user);
     	
     	//Si no le pusieron permiso
     	if (!$this->getPermisos()) $this->setPermisos("ROLE_SUPER_ADMIN");
     	if (!$this->getPrioridad()) $this->setPrioridad(0);
     	
     	
		if ($entity) {
			$this->setEntidad(get_class($entity));
			$this->setIdEntidad($entity->getId());
			$this->setLog($msj.": ".$entity);
		}
		else{
			$this->setLog($msj);
		}
		
     	
     	
     	$em = $this->doctrine->getEntityManager();
     	$em->persist($this);
     	$em->flush();
    
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
}