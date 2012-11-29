<?php

namespace Decoyeso\EstadisticaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('DecoyesoEstadisticaBundle:Default:index.html.twig', array('name' => "hola"));
    }
    
    
}
