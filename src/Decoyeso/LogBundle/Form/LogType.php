<?php

namespace Decoyeso\LogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class LogType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder

            ->add('log','textarea',array('label'=>"Mensaje"))
            ->add('permisos', 'choice', array(
            				
            				'choices' =>array(
            						"ROLE_ADMIN" => "TODOS",
            						"ROLE_SUPER_ADMIN" => "GERENCIA",
            						"ROLE_ADMINISTRACION" => "ADMINISTRACION",
            						"ROLE_DEPOSITO" => "DEPOSITO",          						
            						),"label"=>"Quién puede ver éste mensaje?"))
            ->add('prioridad', 'choice', array(
            				
            				'choices' =>array(
            						1=> "BAJA",
            						2=> "MEDIA",
            						3=> "ALTA",        						
            						)))
        ;
    }

    public function getName()
    {
        return 'log';
    }
}
