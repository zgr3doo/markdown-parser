<?php

namespace MarkdownParser;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', array(
            'secret' => 'S0ME_SECRET'
        ));

        $c->register('kernel', 'MarkdownParser\Kernel');
        $c->register('parsedown', 'Parsedown');
        $c->register('finder', 'Symfony\Component\Finder\Finder');
        $c->register('logger', 'Monolog\Logger')->addArgument('MarkdownLogger');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }
}