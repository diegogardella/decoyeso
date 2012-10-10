<?php

namespace Decoyeso\LogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('LogBundle:Default:index.html.twig', array('name' => $name));
    }
}
