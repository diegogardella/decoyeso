<?php

namespace Decoyeso\StockBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MovimientoStockInsumoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('accion','hidden')
            ->add('elemento','entity',array('label'=>'Insumos','class'=>'Decoyeso\\ProductoBundle\\Entity\\Insumo'))            
            ->add('observacion')
            ->add('cantidad')
        ;
    }

    public function getName()
    {
        return 'movimientostock';
    }
}
