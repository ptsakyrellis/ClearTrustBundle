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
 * Date: 29/02/2016
 * Time: 11:09
 */

namespace AcToulouse\ClearTrustBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class ClearTrustToken extends AbstractToken
{

    public function __construct($user = null, $attributes = array(), $roles = array() )
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
        return ($this->hasAttribute('cn'))? $this->getAttribute('cn') : $this->getUsername();
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