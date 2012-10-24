<?php

namespace Decoyeso\StockBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class StockProductoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('accion')
            ->add('motivo')
            ->add('observacion')
            ->add('cantidad')
            ->add('fechaHora')
            ->add('cantidadTotal')
            ->add('producto')
            ->add('usuario')
        ;
    }

    public function getName()
    {
        return 'decoyeso_stockbundle_stockproductotype';
    }
}
