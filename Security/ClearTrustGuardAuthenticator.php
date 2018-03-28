<?php
namespace AcToulouse\ClearTrustBundle\Security;

use AcToulouse\ClearTrustBundle\Security\Authentication\Token\ClearTrustToken;
use AcToulouse\ClearTrustBundle\Service\ClearTrust;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;


class ClearTrustGuardAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ClearTrust
     */
    protected $cleartrust;

    public function __construct(ClearTrust $cleartrust)
    {
        $this->cleartrust = $cleartrust;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $entryPoint = $this->get('cleartrust.security.authentication.entry_point');

        return $entryPoint->start($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        if (!$this->supports($request)) {
            return null;
        }

        return $this->cleartrust->getAttributes($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Should load user from db
        return $userProvider->loadUserByUsername($credentials['uid']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $token = new ClearTrustToken($user);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return $this->cleartrust->isAuthenticated($request);
    }
}
