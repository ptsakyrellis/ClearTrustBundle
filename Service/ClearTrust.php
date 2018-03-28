<?php

namespace AcToulouse\ClearTrustBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class ClearTrust
{
    private $rsaRemoteUser;
    private $rsaCookieName;
    private $attributeDefinitions = array(
        'uid' => array('header' => 'ct-remote-user', 'multivalue' => false),
        'cn' => array('header' => 'cn', 'multivalue' => false),
        'sn' => array('header' => 'ctln', 'multivalue' => false),
        'givenName' => array('header' => 'ctfn', 'multivalue' => false),
        'mail' => array('header' => 'ctemail', 'multivalue' => false),
        'dn' => array('header' => 'ctdn', 'multivalue' => false),
        'numen' => array('header' => 'employeeNumber', 'multivalue' => false),
        'rne' => array('header' => 'rne', 'multivalue' => false),
        'typeNsi' => array('header' => 'typensi', 'multivalue' => false),
        'title' => array('header' => 'title', 'multivalue' => false),
        'grade' => array('header' => 'grade', 'multivalue' => false),
        'dateNaissance' => array('header' => 'datenaissance', 'multivalue' => false),
        'civilite' => array('header' => 'codecivilite', 'multivalue' => false),
        'FrEduFonctAdm' => array('header' => 'FrEduFonctAdm', 'multivalue' => false),
        'groupes' => array('header' => 'ctgrps', 'multivalue' => true,),
        'FrEduResDel' => array('header' => 'FrEduResDel', 'multivalue' => true,),
        'FrEduGestResp' => array('header' => 'FrEduGestResp', 'multivalue' => true,),
        'FrEduRne' => array('header' => 'FrEduRne', 'multivalue' => true,),
        'FrEduRneResp' => array('header' => 'FrEduRneResp', 'multivalue' => true,),
    );

    public function __construct($rsaRemoteUser, $rsaCookieName, $attributeDefinitions = null)
    {
        $this->rsaRemoteUser = $rsaRemoteUser;
        $this->rsaCookieName = $rsaCookieName;
        if (is_array($attributeDefinitions)) {
            foreach ($attributeDefinitions as $name => $def) {
                $def['alias'] = $name;
                $this->addAttributeDefinition($def);
            }
        }
    }

    public function getAttributes(Request $request)
    {
        $attributes = array();
        if ($this->isAuthenticated($request)) {
            foreach ($this->getAttributeDefinitions() as $name => $def) {
                $value = $this->getAttribute($request, @$def['header']);
                $attributes[$name] = ($def['multivalue'] && !empty($value)) ? explode(',', $value) : $value;
            }
        }

        return $attributes;
    }

    public function getAttributeDefinitions()
    {
        return $this->attributeDefinitions;
    }

    function addAttributeDefinition($def)
    {
        if (!isset($def['multivalue'])) {
            $def['multivalue'] = false;
        }
        $this->attributeDefinitions[$def['alias']] = $def;
    }

    private function getAttribute($request, $attribute)
    {
        return $request->headers->get(strtolower($attribute), null);
    }

    public function isAuthenticated(Request $request)
    {
        return (bool)($this->getRemoteUser($request) && $this->hasRsaCookie($request));
    }

    public function getRemoteUser(Request $request)
    {
        return $this->getAttribute($request, $this->rsaRemoteUser);
    }

    private function hasRsaCookie($request)
    {
        return (bool)$request->cookies->has($this->rsaCookieName);
    }
}