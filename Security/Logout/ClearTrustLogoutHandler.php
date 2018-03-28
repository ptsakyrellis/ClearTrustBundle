<?php

namespace AcToulouse\ClearTrustBundle\Security\Logout;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class ClearTrustLogoutHandler implements LogoutSuccessHandlerInterface
{
    private $logoutTargetUrl;

    public function __construct($logoutTargetUrl)
    {
        $this->logoutTargetUrl = $logoutTargetUrl;
    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        $logoutUrl = empty($this->logoutTargetUrl) ? $request->headers->get('referer') : $this->logoutTargetUrl;
        $response = new RedirectResponse($logoutUrl);

        return $response;
    }
}