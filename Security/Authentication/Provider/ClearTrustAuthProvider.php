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

namespace AcToulouse\ClearTrustBundle\Security\Authentication\Provider;

use AcToulouse\ClearTrustBundle\Security\Authentication\Token\ClearTrustToken;
use AcToulouse\ClearTrustBundle\Security\User\Provider\ClearTrustUserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ClearTrustAuthProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    /**
     * ClearTrustAuthProvider constructor.
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param TokenInterface $token
     * @return ClearTrustToken|null
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }
        try {
            $user = $this->retrieveUser($token);
            $authenticatedToken = new ClearTrustToken($user, $token->getAttributes());
            $authenticatedToken->setAuthenticated(true);

            return $authenticatedToken;
        } catch (\Exception $e) {
            throw(new AuthenticationException('Utilisateur non trouvé'));
        }
    }

    private function retrieveUser(TokenInterface $token)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());

            if (!$user instanceof UserInterface) {
                throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
            }
        } catch (UsernameNotFoundException $notFound) {
            if ($this->userProvider instanceof ClearTrustUserProviderInterface) {
                $user = $this->userProvider->createUser($token);
                if ($user === null) {
                    $user = $token->getUsername();
                }
            } else {
                throw $notFound;
            }
        }

        // todo peut mieux fare pour rendre le code réutilisable
        $this->userProvider->addRolesFromClearTrustAttributes($user, $token);

        return $user;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof ClearTrustToken;
    }
}