<?php

namespace Coobix\AdminBundle\Controller;



namespace Coobix\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Coobix\UserBundle\Entity\Admin;


/**
 * Admin controller.
 *
 * @Route("/")
 */
class AdminController extends Controller
{
    /**
     * Admin Home.
     *
     * @Route("", name="admin_dashboard")
     * @Template()
     */
    public function indexAction()
    {
    	return $this->redirect($this->generateUrl('cliente'));
    }
    
    
   
}
