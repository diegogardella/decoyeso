<?php

namespace Decoyeso\UsuarioBundle\Form;


use Symfony\Component\Form\FormBuilder;
//use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

use Symfony\Component\Form\AbstractType;



class ProfileUsuarioType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
	

        $builder
            ->add('nombre','text', array('label'=>'Nombre'))
            ->add('apellido','text', array('label'=>'Apellido', 'required'=>false))
            ->add('telefono','text', array('label'=>'Teléfono', 'required'=>false))
            ->add('celular','text', array('label'=>'Celular', 'required'=>false))
            ->add('email','text', array('label'=>'Email', 'required'=>false))


		;
		
		
    }

    public function getName()
    {
        return 'usuario';
    }
}
