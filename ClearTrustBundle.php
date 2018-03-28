<?php

namespace AcToulouse\ClearTrustBundle;

use AcToulouse\ClearTrustBundle\DependencyInjection\Security\ClearTrustFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ClearTrustBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ClearTrustFactory());
    }
}
