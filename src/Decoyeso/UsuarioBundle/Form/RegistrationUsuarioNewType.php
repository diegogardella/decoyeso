<?php

namespace Decoyeso\UsuarioBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationUsuarioNewType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
	

        $builder
            ->add('nombre','text', array('label'=>'Nombre'))
            ->add('apellido','text', array('label'=>'Apellido', 'required'=>false))
            ->add('telefono','text', array('label'=>'Teléfono', 'required'=>false))
            ->add('celular','text', array('label'=>'Celular', 'required'=>false))
            ->add('email','email', array('label'=>'Email'))
            ->add('username','text', array('label'=>'Nombre de usuario'))
            ->add('plainPassword','password', array('label'=>'Contraseña'))
		;
		
		//parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'usuarioRegistration';
    }
}