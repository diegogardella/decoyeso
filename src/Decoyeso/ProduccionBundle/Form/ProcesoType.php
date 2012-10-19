<?php

namespace Decoyeso\ProduccionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProcesoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('numero')
            ->add('nombre')
            ->add('estado')
            ->add('fechaCreado')
            ->add('fechaActualizado')
        ;
    }

    public function getName()
    {
        return 'decoyeso_produccionbundle_procesotype';
    }
}
