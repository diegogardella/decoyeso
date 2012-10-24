<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new Decoyeso\ClientesBundle\ClientesBundle(),
        	new Coobix\AdminBundle\CoobixAdminBundle(),
            new Decoyeso\PedidoBundle\PedidoBundle(),
        	new FOS\UserBundle\FOSUserBundle(),

            new Decoyeso\UsuarioBundle\UsuarioBundle(),
            new Decoyeso\LogBundle\LogBundle(),
	    	new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Coobix\BuscadorBundle\BuscadorBundle(),
        	new Spraed\PDFGeneratorBundle\SpraedPDFGeneratorBundle(),
            new Decoyeso\UbicacionBundle\UbicacionBundle(),
        	new Decoyeso\ProductoBundle\ProductoBundle(),

            new Decoyeso\ObraBundle\DecoyesoObraBundle(),
            new Decoyeso\ProduccionBundle\DecoyesoProduccionBundle(),
            new Decoyeso\StockBundle\StockBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
