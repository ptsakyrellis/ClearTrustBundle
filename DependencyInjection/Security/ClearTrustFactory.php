<?php
namespace AcToulouse\ClearTrustBundle\DependencyInjection\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class ClearTrustFactory implements SecurityFactoryInterface
{

    public function create(ContainerBuilder $container, $id, $config, $userProviderId, $defaultEntryPoint)
    {
        $providerId = $this->createAuthProvider($container, $id, $userProviderId);
        $entryPointId = $this->createEntryPoint($container, $id, $defaultEntryPoint);
        $listenerId = $this->createListener($container, $id);

        return array($providerId, $listenerId, $entryPointId);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'cleartrust';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $userProviderId)
    {
        $providerId = 'cleartrust.security.authentication.provider.' . $id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('cleartrust.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId));
        return $providerId;
    }

    protected function createEntryPoint(ContainerBuilder $container, $id, $defaultEntryPoint)
    {
        if (null !== $defaultEntryPoint) {
            return $defaultEntryPoint;
        }
        $entryPointId = 'cleartrust.security.authentication.entry_point.' . $id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('cleartrust.security.authentication.entry_point'));
        return $entryPointId;
    }

    protected function createListener(ContainerBuilder $container, $id)
    {
        $listenerId = 'cleartrust.security.authentication.listener' . $id;
        $container->setDefinition($listenerId, new DefinitionDecorator('cleartrust.security.authentication.listener'));

        return $listenerId;
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}