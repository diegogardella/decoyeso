<?php

namespace Decoyeso\UsuarioBundle\Form;


use Symfony\Component\Form\FormBuilder;
//use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use Symfony\Component\Form\AbstractType;



class CambiarPermisosUsuarioType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
	

        $builder
                         ->add('permisos','choice',
            		array(
            			'choices' =>array(
            						"ROLE_SUPER_ADMIN"=> "SUPER ADMINISTRADOR",
            						"ROLE_ADMINISTRACION"=> "USUARIO ADMINISTRACION",
            						"ROLE_DEPOSITO"=> "USUARIO DEPOSITO",
           						
            						), 
            				'required'=> true,
            				
            				'label'=>'Permisos'))
		;
		
		
    }

    public function getName()
    {
        return 'usuario';
    }
}