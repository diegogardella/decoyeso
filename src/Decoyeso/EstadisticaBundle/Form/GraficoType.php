<?php

namespace Decoyeso\EstadisticaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class GraficoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('nombre')
        ;
    }

    public function getName()
    {
        return 'decoyeso_estadisticabundle_graficotype';
    }
}
