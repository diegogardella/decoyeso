<?php

namespace Decoyeso\ProductoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ServicioType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('unidad')
            ->add('costo', 'text', array('label'=>"Costo"))
            ->add('precio', 'text', array('label'=>"Precio"))
            ->add('productos','hidden',array('property_path'=>false))
            ->add('insumos','hidden',array('property_path'=>false))
        ;
    }

    public function getName()
    {
        return 'servicio';
    }
}
