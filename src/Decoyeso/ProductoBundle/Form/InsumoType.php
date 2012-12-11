<?php

namespace Decoyeso\ProductoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class InsumoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('unidad')
            ->add('costo', 'text', array('label'=>"Costo"))
            ->add('precio', 'text', array('label'=>"Precio"))
        ;
    }

    public function getName()
    {
        return 'insumo';
    }
}
