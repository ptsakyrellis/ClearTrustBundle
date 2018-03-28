<?php

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

/**
 * Authentification d'un utilisateur via RSA ClearTrust Trusted Headers
 */
class ClearTrustAuthProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * ClearTrustAuthProvider constructor.
     *
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @param TokenInterface $token
     *
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

    /**
     *
     * @param TokenInterface $token
     * @return UserInterface
     * @throws UsernameNotFoundException
     */
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

    /**
     *
     * @param TokenInterface $token
     *
     * @return boolean
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof ClearTrustToken;
    }
}