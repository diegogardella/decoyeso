<?php

namespace Decoyeso\ProduccionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProcesoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add("estado","choice", array( 'choices' =>array(
            		0=> "No Iniciado",
            		1=> "En Ejecución",
            		2=> "Finalizado",
            ),
            		"label" => "Estado"))
            		
            ->add('fechaInicio','date', array("format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Fecha de inicio'))
        ;
    }

    public function getName()
    {
        return 'procesotype';
    }
}
