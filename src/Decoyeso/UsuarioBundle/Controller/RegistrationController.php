<?php

namespace Decoyeso\UsuarioBundle\Controller;


use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\RegistrationController as BaseController;


/**
 * User Registration controller.
 */
class RegistrationController extends BaseController
{
	
	/**
	 * Front Controllers.
	 */
	
	public function registerAction()
	{
		$form = $this->container->get('fos_user.registration.form');
		$formHandler = $this->container->get('fos_user.registration.form.handler');
		$confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
	
		$process = $formHandler->process($confirmationEnabled);
		if ($process) {
			$user = $form->getData();
	
			if ($confirmationEnabled) {
				$this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
				$route = 'fos_user_registration_check_email';
			} else {
				$this->authenticateUser($user);
				$route = 'fos_user_registration_confirmed';
			}
	
			$this->setFlash('fos_user_success', 'registration.flash.user_created');
			$url = $this->container->get('router')->generate($route);
			
			
			
			
			
			
	
			return new RedirectResponse($url);
		}
	
		return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
				'form' => $form->createView(),
				'theme' => $this->container->getParameter('fos_user.template.theme'),
		));
	}
	
	    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:checkEmailError.html.'.$this->getEngine());
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:checkEmail.html.'.$this->getEngine(), array(
            'user' => $user,
        ));
    }
	//Esto puede pasar por que actualiza la pagina después de registrarse
	//Tambien por que hacen un submit que de un mail que no existe
	//if ($email == "") {
	/*
	 return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:checkEmailError.html.'.$this->getEngine(), array(
	 		'email' => $email,
	 ));
	*/
	//}
	//else
	
    

	
	/**
	 * Admin Controllers.
	 */
	
}
