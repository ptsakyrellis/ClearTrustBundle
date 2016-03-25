<?php
/**
 * Copyright MEN - Rectorat de Toulouse
 *          DSI - Développements académiques - dsi {at} ac-toulouse.fr
 *
 * (22/03/2015) 
 *
 * Contributeur(s) :
 * Bertrand Ailleres dsi.dsi@ac-toulouse.fr
 *
 * Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
 * respectant les principes de diffusion des logiciels libres. Vous pouvez
 * utiliser, modifier et/ou redistribuer ce programme sous les conditions
 * de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA 
 * sur le site "http://www.cecill.info".
 * 
 * En contrepartie de l'accessibilité au code source et des droits de copie,
 * de modification et de redistribution accordés par cette licence, il n'est
 * offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
 * seule une responsabilité restreinte pèse sur l'auteur du programme,  le
 * titulaire des droits patrimoniaux et les concédants successifs.
 *
 * A cet égard  l'attention de l'utilisateur est attirée sur les risques
 * associés au chargement,  à l'utilisation,  à la modification et/ou au
 * développement et à la reproduction du logiciel par l'utilisateur étant 
 * donné sa spécificité de logiciel libre, qui peut le rendre complexe à 
 * manipuler et qui le réserve donc à des développeurs et des professionnels
 * avertis possédant  des  connaissances  informatiques approfondies.  Les
 * utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
 * logiciel à leurs besoins dans des conditions permettant d'assurer la
 * sécurité de leurs systèmes et ou de leurs données et, plus généralement, 
 * à l'utiliser et l'exploiter dans les mêmes conditions de sécurité. 
 *
 * Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
 * pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
 * termes.
 */

namespace AcToulouse\ClearTrustBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('clear_trust');

        $rootNode
            ->children()
                ->scalarNode('rsa_remote_user')->end()
                ->scalarNode('rsa_cookie_name')->end()
                ->scalarNode('logout_target_url')->end()
                ->scalarNode('login_target_url')->end()
            ->end()
            ->fixXmlConfig('attribute_definition')
            ->children()
                ->arrayNode('attribute_definitions')
                ->useAttributeAsKey('alias')
                ->prototype('array')
                    ->children()
                        ->scalarNode('header')->isRequired()->end()
                        ->booleanNode('multivalue')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
