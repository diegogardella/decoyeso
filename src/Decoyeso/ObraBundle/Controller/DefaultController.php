<?php

namespace Decoyeso\ObraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('DecoyesoObraBundle:Default:index.html.twig', array('name' => $name));
    }
}
