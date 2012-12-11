<?php

namespace Decoyeso\ProductoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        	->add('tipo','choice',array('label'=>'Tipo','choices'=>array(0=>"Placas de yeso",1=>"Molduras de yeso",2=>"Otro")))
            ->add('nombre')
            ->add('descripcion')
            ->add('unidad')
            ->add('costo', 'text', array('label'=>"Costo"))
            ->add('precio', 'text', array('label'=>"Precio"))
            ->add('insumos','hidden',array('property_path'=>false))
            ->add('productos','hidden',array('property_path'=>false))
            
        ;
    }

    public function getName()
    {
        return 'producto';
    }
}
