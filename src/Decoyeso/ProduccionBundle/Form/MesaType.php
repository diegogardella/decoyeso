<?php

namespace Decoyeso\ProduccionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MesaType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('capacidad')
            ->add('observacion')
        ;
    }

    public function getName()
    {
        return 'mesatype';
    }
}
