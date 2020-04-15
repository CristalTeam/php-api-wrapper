<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;

use Cristal\ApiWrapper\Bridges\Symfony\DependencyInjection\ManagerConnectionPass;
use Cristal\ApiWrapper\Bridges\Symfony\DependencyInjection\RepositoryPass;
use Cristal\ApiWrapper\Bridges\Symfony\DependencyInjection\WrapperExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiWrapperBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ManagerConnectionPass());
        $container->addCompilerPass(new RepositoryPass());
    }

    public function getContainerExtension()
    {
        return new WrapperExtension();
    }
}
