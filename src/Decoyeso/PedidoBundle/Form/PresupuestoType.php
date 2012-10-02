<?php

namespace Decoyeso\PedidoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PresupuestoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        
        ->add('nombre', 'text', array('label'=>"Nombre"))
        	
            ->add('pedido','entity',array('class'=>'Decoyeso\\PedidoBundle\\Entity\\Pedido','label'=>'Pedido','multiple'=>false, 'expanded'=>false))
            
            ->add('mostrarColumnas','choice',
            		
            		array(
            				
            				'choices' =>array(
            						0=> "DESIGNACION",
            						1=> "UNIDAD",
            						2=> "CANTIDAD",
            						3=> "PRECIO UNIT.",
            						4=> "PRECIO VTA S/IVA",
            						5=> "PRECIO VTA C/IVA",
            						6=> "PRECIO TOTAL"            						
            						),
            				
            				'multiple'=> "true",
            				"expanded"=> "true",
            				"label"=>"Precios Unitarios"))
         
            ->add('subTotal', 'text', array('label'=>"",'attr'=> array('class'=>'inputCorto')))
            ->add('total', 'text', array('label'=>"",'attr'=> array('class'=>'inputCorto')))
            
            ->add('precioEntrega', 'text', array('label'=>"", 'required'=> false,'attr'=> array('class'=>'input190'), 'required'=>false))
            
            ->add('precioTextoEntrega', 'text', array('label'=>"",'required'=> false,'attr'=> array('class'=>'input190'), 'required'=>false))
            ->add('precioSaldo', 'text', array('label'=>"",'required'=> false,'attr'=> array('class'=>'input190'), 'required'=>false))
            ->add('formaPago', 'text', array('label'=>"",'required'=> false,'attr'=> array('class'=>''), 'required'=>false))
            
            ->add('observacion','textarea',array('label'=>"",'required'=> false,'attr'=> array('class'=>'NobservacionPresupuesto')))
            
            ->add("mostrarFirmas","choice", array( 'choices' =>array(
            						0=> "Si",
            						1=> "NO",            						
            						),
            		"label" => "Mostrar Firmas"))
            
            
        ;
    }

    public function getName()
    {
        return 'decoyeso_pedidobundle_presupuestotype';
    }
    
    public function getDefaultOptions(array $options)
    {
    	return array(
    			'data_class' => 'Decoyeso\\PedidoBundle\\Entity\\Presupuesto',
    			'intention'  => 'presupuesto',
    			'csrf_protection' => true,
    			'csrf_field_name' => '_token',
    			// una clave única para ayudar a generar el elemento secreto
    			'intention'	=> 'presupuesto_item',
    			
    			
    	);
    }
    
    
}
