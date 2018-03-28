<?php

namespace AcToulouse\ClearTrustBundle\Security\Firewall;

use AcToulouse\ClearTrustBundle\Security\Authentication\Token\ClearTrustToken;
use AcToulouse\ClearTrustBundle\Service\ClearTrust;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Listener pour l'authentification RSA ClearTrust Trusted Headers
 */
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

        if (!$this->clearTrust->isAuthenticated($request)) {
            return;
        }

        $username = $this->clearTrust->getRemoteUser($request);

        if (null !== $token = $this->tokenStorage->getToken()) {
            if ($token instanceof ClearTrustToken && $token->isAuthenticated() && $token->getUsername() === $username) {
                return;
            }
        }

        $attributes = $this->clearTrust->getAttributes($request);

        try {
            $authenticatedToken = $this->authenticationManager->authenticate(new ClearTrustToken($username, $attributes));
            $this->tokenStorage->setToken($authenticatedToken);
        } catch (AuthenticationException $e) {
            $this->tokenStorage->setToken(null);

            if ($this->authenticationEntryPoint) {
                return $event->setResponse($this->authenticationEntryPoint->start($request, $e));
            }
        }
    }
}