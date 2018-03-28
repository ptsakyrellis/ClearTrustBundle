<?php

namespace AcToulouse\ClearTrustBundle\Security\Authentication\EntryPoint;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ClearTrustEntryPoint implements AuthenticationEntryPointInterface
{
    private $loginTargetUrl;

    public function __construct($loginTargetUrl)
    {
        $this->loginTargetUrl = $loginTargetUrl;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $loginUrl = empty($this->loginTargetUrl) ? $request->headers->get('referer') : $this->loginTargetUrl;
        $response = new RedirectResponse($loginUrl);

        return $response;
    }
}