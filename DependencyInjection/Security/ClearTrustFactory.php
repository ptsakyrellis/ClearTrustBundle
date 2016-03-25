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

/**
 * Created by PhpStorm.
 * User: bailleres
 * Date: 29/02/2016
 * Time: 17:24
 */

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
        $providerId = 'cleartrust.security.authentication.provider.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('cleartrust.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
        ;
        return $providerId;
    }

    protected function createEntryPoint(ContainerBuilder $container, $id, $defaultEntryPoint)
    {
        if (null !== $defaultEntryPoint)
        {
            return $defaultEntryPoint;
        }
        $entryPointId = 'cleartrust.security.authentication.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('cleartrust.security.authentication.entry_point'))
        ;
        return $entryPointId;
    }

    protected function createListener(ContainerBuilder $container, $id)
    {
        $listenerId = 'cleartrust.security.authentication.listener'.$id;
        $container->setDefinition($listenerId, new DefinitionDecorator('cleartrust.security.authentication.listener'));

        return $listenerId;
    }
	
	public function addConfiguration(NodeDefinition $node)
    {
    }
}