<?php

namespace Decoyeso\LogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class LogType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('log')
            ->add('fechaCreacion')
            ->add('usuario')
        ;
    }

    public function getName()
    {
        return 'log';
    }
}
