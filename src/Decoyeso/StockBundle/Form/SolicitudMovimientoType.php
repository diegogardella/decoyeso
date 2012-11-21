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
            ->add('fechaHoraRequerido','datetime', array("date_format"=>"d-m-Y",'pattern'=> '{{ day }}{{ month }}{{ year }}','label'=>'Fecha requerido'))
        	->add('direccionDestino','text',array('label'=>"Destino"))
            ->add('observacion')
        ;
    }

    public function getName()
    {
        return 'solicitudmovimiento';
    }
}
