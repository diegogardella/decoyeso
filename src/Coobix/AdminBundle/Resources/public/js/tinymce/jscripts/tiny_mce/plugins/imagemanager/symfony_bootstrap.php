<?php
//echo $basepath;
//var/www/comunidad2/src/Coobix/AdminBundle/Resources/public/js/tinymce/jscripts/tiny_mce/plugins/imagemanager/classes/
//exit();
/*
require ('../../../../../../../../../../../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php');


$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();



$loader->registerNamespaces(array(
	'Symfony'          => array(
			__DIR__.'/../../../../../../../../../../../vendor/symfony/src', 
			__DIR__.'/../../../../../../../../../../../vendor/bundles'),
));

$loader->register();


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class mcImageController extends Controller
{
	 public function getSes() {
		$session = $this->getRequest()->getSession();
		
		//$request = Request::createFromGlobals();
		
		//$uri = $request->getPathInfo();
		//$session = $request->getSession();
		print_r($session);
		$username_aux = $session->get('username_aux');
		
		
		// in another controller for another request
		//$username_aux = $session->get('username_aux');
		
		print_r($username_aux);
	}
	
	

}

echo "sess". $_SESSION['username_aux'];

$m = new mcImageController();
//$m->getSes();
//mcImageController::getSession();
*/
//echo "sess". $_SESSION['username_aux'];
$carpeta_de_usuario = '../../../../../../../../../../../web/uploads/'.$_SESSION['username_aux'];

$mcImageManagerConfig['filesystem.path'] = $carpeta_de_usuario;
$mcImageManagerConfig['filesystem.rootpath'] = $carpeta_de_usuario;
//exit();
?>
