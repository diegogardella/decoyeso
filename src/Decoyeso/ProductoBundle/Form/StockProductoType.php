<?php

namespace Decoyeso\ProductoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class StockProductoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('accion','hidden')
            ->add('producto')            
            ->add('motivo','choice',array('choices'=>array('1'=>'Nuevo producto','2'=>'Regresó de Obra')))
            ->add('observacion')
            ->add('cantidad')
        ;
    }

    public function getName()
    {
        return 'stockproducto';
    }
}
