<?php

namespace Decoyeso\UbicacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('UbicacionBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function cargarDepartamentoAction(){
    	
    	$em=$this->getDoctrine()->getEntityManager();
    	$provincia = $this->getRequest()->request->get('prov');
    	
    	
    	$entities = $em->createQuery('select d from UbicacionBundle:Departamento d where d.provincia= :provincia order by d.nombre asc')->setParameter('provincia',$provincia)->getResult();
    	
    	$i=0;
    	$departamentos=array();
    	
    	foreach($entities as $entity){
    		$departamentos[$i]['id']=$entity->getId();
    		$departamentos[$i]['nombre']=$entity->getNombre();
    		$i++;
    	}
    	

		
    	$datos=json_encode($departamentos);//jscon encode the array
    	return new Response($datos);
    	
    }
    
    public function cargarLocalidadAction(){
    
    	$em=$this->getDoctrine()->getEntityManager();
    	$departamento = $this->getRequest()->request->get('depa');
    
    
    	$entities = $em->createQuery('select l from UbicacionBundle:Localidad l where l.departamento= :departamento order by l.nombre asc')->setParameter('departamento',$departamento)->getResult();
    
    	$localidades=array();
    	$i=0;
    	foreach($entities as $entity){
    		$localidades[$i]['id']=$entity->getId();
    		$localidades[$i]['nombre']=$entity->getNombre();
    		$i++;
    	}
    
    
    
    	$datos=json_encode($localidades);//jscon encode the array
    	return new Response($datos);
    
    }
}
