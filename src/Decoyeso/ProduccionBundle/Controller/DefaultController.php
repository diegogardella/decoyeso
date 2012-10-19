<?php

namespace Decoyeso\ProduccionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('DecoyesoProduccionBundle:Default:index.html.twig', array('name' => $name));
    }
}
