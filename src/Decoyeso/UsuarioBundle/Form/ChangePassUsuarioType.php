<?php

namespace Decoyeso\UsuarioBundle\Form;


use Symfony\Component\Form\FormBuilder;
//use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use Symfony\Component\Form\AbstractType;



class ChangePassUsuarioType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
	

        $builder
             ->add('plainPassword','password', array('label'=>'Nueva Contraseña'))
		;
		
		
    }

    public function getName()
    {
        return 'usuario';
    }
}