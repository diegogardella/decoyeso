<?php

namespace Decoyeso\ClientesBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ClienteType extends AbstractType
{
	
    public function buildForm(FormBuilder $builder, array $options)
    {

    	$provincia=$builder->getData()->getProvincia()->getId();
    	$departamento=$builder->getData()->getDepartamento()->getId();

    	
        $builder
        	->add('tipo', 'choice', array('choices'=>
        		array(1 => 'Persona Física', 2 => 'Organización')
        	))
        	->add('nombre', 'text', array('label'=> 'Apellido, Nombre'))
        	->add('dni', 'text', array('label'=> 'DNI', 'required'=>false))
        	->add('cuitOcuil', 'text', array('label'=> 'CUIL', 'required'=>false))   
            ->add('telefono', 'text', array('label'=> 'Teléfono', 'required'=>false))
            ->add('celular', 'text', array('label'=> 'Celular', 'required'=>false))
            ->add('email', 'email', array('label'=> 'Email', 'required'=>false))
            ->add('provincia', 'entity',array(
            		'class'=>'Decoyeso\\UbicacionBundle\\Entity\\Provincia','label'=>'Provincia',
            		'query_builder'=>function(EntityRepository $em){
            				return $em->createQueryBuilder('p')->orderBy('p.nombre','asc');
            			}
            		))
            ->add('departamento','entity',array(
            		'class'=>'Decoyeso\\UbicacionBundle\\Entity\\Departamento','label'=>'Departamento',
            		'query_builder'=>function(EntityRepository $em) use ($provincia){
            				
            					if($provincia!=null){
            						return $em->createQueryBuilder('d')->where('d.provincia= :provincia')->setParameter('provincia', $provincia)->orderBy('d.nombre','asc');
            					}else{
            						return $em->createQueryBuilder('d')->orderBy('d.nombre','asc');
            					}
            				
            			}
            		))
            ->add('localidad', 'entity',array(
            		'class'=>'Decoyeso\\UbicacionBundle\\Entity\\Localidad','label'=>'Localidad',
            		'query_builder'=>function(EntityRepository $em) use ($departamento){
            		
		            		
		            		return $em->createQueryBuilder('l')->where('l.departamento= :departamento')->setParameter('departamento', $departamento)->orderBy('l.nombre','asc');
		            		
            		
            			}
            		))
            ->add('direccionBarrio', 'text', array('label'=> 'Dirección, Barrio', 'required'=>false))
            ->add('direccionCalle', 'text', array('label'=> 'Dirección, Calle', 'required'=>false))
            ->add('direccionNumero', 'text', array('label'=> 'Dirección, Número', 'required'=>false))
            

            ->add('observaciones','textarea', array('label'=>'Observaciones', 'required'=>false, 'attr'=> array('class'=>'no_editor_textarea')))

        ;

    }

    public function getName()
    {
        return 'cliente';
    }
    
    
}
