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
 * Date: 01/03/2016
 * Time: 10:32
 */

namespace AcToulouse\ClearTrustBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class ClearTrust
{

    private $rsaRemoteUser;
    private $rsaCookieName;
    private $attributeDefinitions = array(
        'uid'           => array('header'   =>  'ct-remote-user',   'multivalue'    => false),
        'cn'            => array('header'   =>  'cn',               'multivalue'    => false),
        'sn'            => array('header'   =>  'ctln',             'multivalue'    => false),
        'givenName'     => array('header'   =>  'ctfn',             'multivalue'    => false),
        'mail'          => array('header'   =>  'ctemail',          'multivalue'    => false),
        'dn'            => array('header'   =>  'ctdn',             'multivalue'    => false),
        'numen'         => array('header'   =>  'employeeNumber',   'multivalue'    => false),
        'rne'           => array('header'   =>  'rne',              'multivalue'    => false),
        'typeNsi'       => array('header'   =>  'typensi',          'multivalue'    => false),
        'title'         => array('header'   =>  'title',            'multivalue'    => false),
        'grade'         => array('header'   =>  'grade',            'multivalue'    => false),
        'dateNaissance' => array('header'   =>  'datenaissance',    'multivalue'    => false),
        'civilite'      => array('header'   =>  'codecivilite',     'multivalue'    => false),
        'FrEduFonctAdm' => array('header'   =>  'FrEduFonctAdm',    'multivalue'    => false),
        'groupes'       => array('header'   =>  'ctgrps',           'multivalue'    => true,),
        'FrEduResDel'   => array('header'   =>  'FrEduResDel',      'multivalue'    => true,),
        'FrEduGestResp' => array('header'   =>  'FrEduGestResp',    'multivalue'    => true,),
        'FrEduRne'      => array('header'   =>  'FrEduRne',         'multivalue'    => true,),
        'FrEduRneResp'  => array('header'   =>  'FrEduRneResp',     'multivalue'    => true,),
    );

    public function __construct($rsaRemoteUser, $rsaCookieName, $attributeDefinitions = null)
    {
        $this->rsaRemoteUser = $rsaRemoteUser;
        $this->rsaCookieName = $rsaCookieName;
        if (is_array($attributeDefinitions))
        {
            foreach($attributeDefinitions as $name => $def)
            {
                $def['alias'] = $name;
                $this->addAttributeDefinition($def);
            }
        }
    }

    public function getAttributes(Request $request)
    {
        $attributes = array();
        if ($this->isAuthenticated($request))
        {
            foreach ($this->getAttributeDefinitions() as $name => $def)
            {
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
        if (!isset($def['multivalue']))
        {
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