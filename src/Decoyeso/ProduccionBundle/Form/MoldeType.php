<?php

namespace Decoyeso\ProduccionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MoldeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('producto')
            ->add('cantidad')
            ->add('observacion')
        ;
    }

    public function getName()
    {
        return 'moldetype';
    }
}
