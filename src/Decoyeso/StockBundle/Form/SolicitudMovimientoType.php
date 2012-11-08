<?php

namespace Decoyeso\StockBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SolicitudMovimientoType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        	->add('pedido')
            ->add('fechaHoraRequerido','date', array("format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Fecha requerido'))
            ->add('observacion')
        ;
    }

    public function getName()
    {
        return 'solicitudmovimiento';
    }
}
