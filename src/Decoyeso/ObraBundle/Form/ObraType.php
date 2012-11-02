<?php

namespace Decoyeso\ObraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ObraType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add("estado","choice", array( 'choices' =>array(
            		0=> "No Iniciada",
            		1=> "En Ejecución",
            		2=> "Finalizada",
            ),
            		"label" => "Estado"))
        ;
    }

    public function getName()
    {
        return 'obratype';
    }
}
