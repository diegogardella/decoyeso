<?php

namespace Decoyeso\ProduccionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SecadorType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('tipo', 'choice', array('choices'=>
            		array( 0 => 'Secador de Placas', 1 => 'Secador de Molduras')
            ))
            ->add('capacidad')
            ->add('tiempoSecado', 'integer', array('label'=> 'Tiempo de Secado (En Dias)'))
            ->add('observacion')
        ;
    }

    public function getName()
    {
        return 'secadortype';
    }
}
