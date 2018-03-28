<?php

namespace AcToulouse\ClearTrustBundle\Security\User\Provider;

use AcToulouse\ClearTrustBundle\Model\ClearTrustUser;
use AcToulouse\ClearTrustBundle\Security\Authentication\Token\ClearTrustToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Tout utilisateur chargé depuis un user provider utilisant ce bundle ClearTrust doit implémenter cette interface.
 */
interface ClearTrustUserProviderInterface extends UserProviderInterface
{
    /**
     * Création de l'utilisateur (par exemple en base) à partir des informations du token ClearTrust
     *
     * @param ClearTrustToken $token
     */
    public function createUser(ClearTrustToken $token);

    /*
     * Mapping des rôles utilisateurs en fonction des attributs ClearTrust
     *
     * @param ClearTrustUser $user
     * @param ClearTrustToken $token
     */
    public function addRolesFromClearTrustAttributes(ClearTrustUser $user, ClearTrustToken $token);
}