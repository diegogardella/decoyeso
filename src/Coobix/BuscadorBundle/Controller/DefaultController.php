<?php

namespace Coobix\BuscadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('BuscadorBundle:Default:index.html.twig', array('name' => $name));
    }
}
