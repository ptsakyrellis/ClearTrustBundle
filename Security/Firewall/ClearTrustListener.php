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
 * Time: 11:20
 */

namespace AcToulouse\ClearTrustBundle\Security\Firewall;

use AcToulouse\ClearTrustBundle\Security\Authentication\Token\ClearTrustToken;
use AcToulouse\ClearTrustBundle\Service\ClearTrust;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class ClearTrustListener implements ListenerInterface
{
    protected $tokenStorage;
    protected $authenticationManager;
    protected $authenticationEntryPoint;
    protected $clearTrust;


    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, AuthenticationEntryPointInterface $authenticationEntryPoint, ClearTrust $clearTrust)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->authenticationEntryPoint = $authenticationEntryPoint;
        $this->clearTrust = $clearTrust;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->clearTrust->isAuthenticated($request))
        {
            return;
        }

        $username = $this->clearTrust->getRemoteUser($request);

        if (null !== $token = $this->tokenStorage->getToken())
        {
            if ($token instanceof ClearTrustToken && $token->isAuthenticated() && $token->getUsername() === $username)
            {
                return;
            }
        }

        $attributes = $this->clearTrust->getAttributes($request);

        try
        {
            $authenticatedToken = $this->authenticationManager->authenticate(new ClearTrustToken($username, $attributes));
            $this->tokenStorage->setToken($authenticatedToken);
        }
        catch (AuthenticationException $e)
        {
            $this->tokenStorage->setToken(null);

            if ($this->authenticationEntryPoint)
            {
                return $event->setResponse($this->authenticationEntryPoint->start($request, $e));
            }
        }
    }
}