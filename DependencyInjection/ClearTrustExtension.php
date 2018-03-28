<?php
namespace AcToulouse\ClearTrustBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ClearTrustExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (isset($config['remote_user_attribute'])) {
            $container->setParameter('clear_trust.remote_user_attribute', $config['remote_user_attribute']);
        }
        if (isset($config['rsa_cookie_name'])) {
            $container->setParameter('clear_trust.rsa_cookie_name', $config['rsa_cookie_name']);
        }
        if (isset($config['logout_target_url'])) {
            $container->setParameter('clear_trust.logout_target_url', $config['logout_target_url']);
        }
        if (isset($config['login_target_url'])) {
            $container->setParameter('clear_trust.login_target_url', $config['login_target_url']);
        }
        if (isset($config['attribute_definitions'])) {
            $container->setParameter('clear_trust.attribute_definitions', $config['attribute_definitions']);
        } else {
            $container->setParameter('clear_trust.attribute_definitions', array());
        }
    }
}
