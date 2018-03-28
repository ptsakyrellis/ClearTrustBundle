<?php
namespace AcToulouse\ClearTrustBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class ClearTrustToken extends AbstractToken
{

    public function __construct($user = null, $attributes = array(), $roles = array())
    {
        if (empty($roles) && $user instanceof UserInterface) $roles = $user->getRoles();
        parent::__construct($roles);
        $this->setUser($user);
        $this->setAttributes($attributes);
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return '';
    }

    public function getDisplayName()
    {
        return ($this->hasAttribute('cn')) ? $this->getAttribute('cn') : $this->getUsername();
    }

    public function getUid()
    {
        return $this->getAttribute('uid');
    }

    public function getSn()
    {
        return $this->getAttribute('sn');
    }

    public function getGivenName()
    {
        return $this->getAttribute('givenName');
    }

    public function getMail()
    {
        return $this->getAttribute('mail');
    }

    public function getDn()
    {
        return $this->getAttribute('dn');
    }

    public function getGroupes()
    {
        return $this->getAttribute('groupes');
    }

    public function getNumen()
    {
        return $this->getAttribute('employeeNumber');
    }

    public function getFrEduRne()
    {
        return $this->getAttribute('FrEduRne');
    }

    public function getFrEduRneResp()
    {
        return $this->getAttribute('FrEduRneResp');
    }

    public function getFrEduFonctAdm()
    {
        return $this->getAttribute('FrEduFonctAdm');
    }

    public function geRne()
    {
        return $this->getAttribute('rne');
    }

    public function getTypeNsi()
    {
        return $this->getAttribute('typeNsi');
    }

    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    public function getGrade()
    {
        return $this->getAttribute('grade');
    }

    public function getDateNaissance()
    {
        return $this->getAttribute('dateNaissance');
    }

    public function getCivilite()
    {
        return $this->getAttribute('civilite');
    }

    public function getAttribute($name)
    {
        $value = parent::getAttribute($name);

        return (is_array($value)) ? $value[0] : $value;
    }

    public function getArrayAttribute($name)
    {
        $value = parent::getAttribute($name);

        return (is_array($value)) ? $value : array($value);
    }
}