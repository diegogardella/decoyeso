<?php

namespace Decoyeso\PedidoBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PedidoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
    	
    	$provincia=$builder->getData()->getProvincia()->getId();
    	$departamento=$builder->getData()->getDepartamento()->getId();
    	
        $builder
        	->add('cliente','entity', array('class'=>'Decoyeso\\ClientesBundle\\Entity\\Cliente','label'=>'Cliente','empty_value'=>"Seleccione cliente",'multiple'=>false, 'expanded'=>false))
        	->add('nombreObra', 'text', array('label'=>'Obra'))
        	->add('provincia','entity',array(
        			'label'=>'Provincia',
        			'read_only'=>true,
        			'class'=>'Decoyeso\\UbicacionBundle\\Entity\\Provincia',
        			'query_builder'=>function(EntityRepository $em){
        				
        				return $em->createQueryBuilder('p')->orderBy('p.nombre','asc');
        			
        			}
        			
        	))
        	
        	->add('departamento','entity',array(
        			'label'=>'Departamento',
        			'read_only'=>true,
        			'class'=>'Decoyeso\\UbicacionBundle\\Entity\\Departamento',
        			'query_builder'=>function(EntityRepository $em) use ($provincia){
        	
        			return $em->createQueryBuilder('d')->where('d.provincia= :provincia')->setParameter('provincia',$provincia)->orderBy('d.nombre','asc');
        	
        	}
        	
        	))
        	
        	->add('localidad','entity',array(
        			'label'=>'Localidad',
        			'read_only'=>true,
        			'class'=>'Decoyeso\\UbicacionBundle\\Entity\\Localidad',
        			'query_builder'=>function(EntityRepository $em) use ($departamento){
        			        	
	        			return $em->createQueryBuilder('l')->where('l.departamento= :departamento')->setParameter('departamento',$departamento)->orderBy('l.nombre','asc');	
    	    	}
        	
        	))
        	        	
        	->add('barrio', 'text', array('label'=>'Barrio', 'required'=>false))
        	->add('calle', 'text', array('label'=>'Calle', 'required'=>false))
        	->add('numeroCalle','text', array('label'=>"Número", 'required'=>false))
        	->add('requiereRelevamiento','choice',array('choices' =>array(0=>"No",1=>"Si"),"label"=>"Requiere relevamiento"))
        	->add('prioridad','choice',array('choices' =>array(1=>"Baja",2=>"Media",3=>"Alta",4=>"Muy alta"),"label"=>"Prioridad"))
        	->add('fechaSolicitado','date', array("format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Fecha de solicitación'))
        	->add('fechaEntrega','date', array("format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Fecha de entrega'))
            ->add('descripcion','textarea',array('label'=>'Descripcion', 'required'=> false, 'attr'=> array('class'=>'no_editor_textarea')))
        ;
    } 

    public function getName()
    {
        return 'pedido';
    }
}
