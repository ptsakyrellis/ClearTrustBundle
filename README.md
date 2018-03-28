# ClearTrustBundle

Ce bundle Symfony permet d'utiliser l'authentification RSA-ClearTrust dans votre projet Symfony, et plus précisément l'utilisation de RSA pour une authentification via des Trusted Headers (cf. https://community.rsa.com/docs/DOC-53978) 

Pré-requis
----------
* PHP 5.6
* Symfony 3

Installation
------------

### 1. Installation avec composer

```bash
$ composer require ptsakyrellis/clear-trust-bundle:dev-master
```

### 2. Activer le bundle dans le kernel

```php
// app/AppKernel.php
<?php
    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new AcToulouse\ClearTrustBundle\ClearTrustBundle(),
        );
    }
```

Configuration
-------------

### 1. Activer la protection de votre application par ClearTrust dans Apache

Le bundle ne vérifie pas que votre application est protégée, c'est votre responsabilité de le faire.

### 2. Paramétrage du firewall

```yml
# app/config/security.yml
security:
    firewalls:
        cleartrust:
            pattern: ^/
            cleartrust: ~
            logout:
                path: /logout 
                success_handler: cleartrust.security.logout.handler
```

### 3. Paramétrage du bundle

```yml
# app/config/config.yml
clear_trust:
    rsa_remote_user: ct-remote-user                       # nom de l'entete contenant le username (uid)
    rsa_cookie_name: CTSESSION                            # nom du cookie RSA
    logout_target_url : https://url_de_deconnexion        # url de retour apres déconnexion
    login_target_url : https://url_de_connexion           # url de connexion
```

### 3. Création du routing

La route de "logout" doit être créée en accord avec la valeur choisie dans le firewall. Il n'y a pas d'action associée à cette route.

```yml
#app/config/routing.yml
logout:
    path: /logout
```

Attributs ClearTrust disponibles
--------------------------------
Par défaut le bundle permet l'accès à de nombreux attributs ClearTrust, accessibles via le token `AcToulouse\ClearTrustBundle\Security\Authentication\/Token\ClearTrustToken.php`. 
Le token fournit des accesseurs pour la plupart des attributs, ainsi que les accesseurs génériques `getAttribute` et `getArrayAttribute`. 

Chaque attribut est identifié par un alias qui sert d'argument à ces methodes. 
Le tableau suivant liste les attributs disponibles lorsqu'ils sont fournis par ClearTrust :

| Attribut             | Alias                |
| -------------------- | -------------------- |
| ct-remote-user       | uid                  |
| ctln                 | sn                   |
| ctfn                 | givenName            |
| ctemail              | mail                 |
| ctdn                 | dn                   |

Si vous souhaitez accéder à des attributs supplémentaires non listés dans ce tableau, vous pouvez les ajouter via la configuration du bundle :

```yml
# app/config/config.yml
clear_trust_:
    # ...
    attribute_deinitions:
        monAttrMono:                                      # alias de l'attribut monovalué
            header: FrEduAttrMono                         # nom de l'attribut monovalué
        monAttrMulti:                                     # alias de l'attribut multivalué
            header: FrEduAttrMulti                        # nom de l'attribut multivalué
            multivalue: true                              # multivalué (séparé par virgules)
```

L'alias d'un attribut est la clé qui permet d'accéder à sa valeur. Par exemple, les valeurs des attributs `FrEduAttrMono` et `FrEduAttrMulti` peuvent être obtenues de cette manière :

```php
$laValeurDeMonAttrMono = $token->getAttribute('monAttrMono');
$lesValeursDeMonAttrMulti = $token->getArrayAttribute(monAttrMulti'); // retourne un tableau contenant les multiples valeurs
```

Gestion des utilisateurs
------------------------

Ce bundle n'inclut pas de fournisseur d'identité, vous devez implémenter le votre en implémentant l'interface `AcToulouse\ClearTrustBundle\Security\User\Provider\ClearTrustUserProviderInterface`.
Les utilisateurs sont créés à la volée (on fait confiance à l'authentification faite par RSA-ClearTrust en amont de l'application) et stockés en base.

### 1. Fournisseur d'identité

```php
<?php 
namespace VotreNamespace\Security;

use VotreNamespace\Entity\MyUser;

use Symfony\Component\Security\Core\User\UserInterface;
use AcToulouse\ClearTrustBundle\Model\ClearTrustUser;
use AcToulouse\ClearTrustBundle\Security\Authentication\Token\ClearTrustToken;
use AcToulouse\ClearTrustBundle\Security\User\Provider\ClearTrustUserProviderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class MyUserProvider implements ClearTrustUserProviderInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createUser(ClearTrustToken $token)
    {
        $user = new MyUser();
        $user->setUsername($token->getUid());
        $user->setEmail($token->getMail());    
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Attribue des roles supplémentaires (facultatif) en fonction des attributs RSA ClearTrust
     *
     * @param ClearTrustUser $user
     *
     * @param ClearTrustToken $token
     */
    public function addRolesFromClearTrustAttributes(ClearTrustUser $user, ClearTrustToken $token)
    {
        // Exemples de roles additionnels créés en fonction des attributs RSA ClearTrust
        if ($token->getAttribute('FrEduFonctAdm') == 'DIR') $user->addRole('ROLE_DIR');
        elseif ($token->getAttribute('FrEduFonctAdm') == 'DEC') $user->addRole('ROLE_DEC');
        elseif ($token->getAttribute('FrEduFonctAdm') == 'DIO') $user->addRole('ROLE_DIO');
        elseif ($token->getAttribute('FrEduFonctAdm') == 'IEN1D') $user->addRole('ROLE_IEN1D');
        elseif ($token->getAttribute('FrEduFonctAdm') == 'ACP') $user->addRole('ROLE_ACP');

        return;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return MyUser
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('VotreNamespace\Entity\MyUser')->findOneBy(array('username' => $username));

        if($user)
        {
            return $user;
        }
        else
        {
            throw new UsernameNotFoundException("User ".$username. " not found.");
        }
    }

    /**
     * Refreshes the user for the account interface.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserInterface)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === 'VotreNamespace\Entity\MyUser';
    }
}
```

Enregistrement du service

```yml
# app/config/services.yml
services:
    my_user_provider:
        class: VotreNamespace\MyUserProvider
        arguments: ["@doctrine.orm.default_entity_manager"]
```

Utilisation dans le composant de sécurité

```yml
# app/config/security.yml
security:	
    providers:
        my_provider:
           id: my_user_provider
           
    firewalls:
        cleartrust:
            # ...
            provider: my_provider
            # ...
```

### 2. Classe utilisateur

Votre classe utilisateur doit étendre la classe `AcToulouse\ClearTrustBundle\Model\ClearTrustUser`. 
Le champ id doit être mappé et déclaré protected. 
Le champ email a été implémenté pour l'exemple et pour correspondre à un utilisateur qui serait créé par le fournisseur d'identité du dessus.

```php
<?php
namespace VotreNamespace\Entity;

use Doctrine\ORM\Mapping as ORM;
use AcToulouse\ClearTrustBundle\Model\ClearTrustUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="MyUser")
 */
class MyUser extends ClearTrustUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    // Si vous définissez un constructeur, vous devez appeler celui du parent
    public function __construct()
    {
        parent::__construct();
        // votre logique métier
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        
        return $this;
    }
}   
```

License
-------------

Copyright MEN - Rectorat de Toulouse
DSI - Développements académiques - dsi {at} ac-toulouse.fr

Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
respectant les principes de diffusion des logiciels libres. Vous pouvez utiliser, 
modifier et/ou redistribuer ce programme sous les conditions 
de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA
sur le site "http://www.cecill.info".

En contrepartie de l'accessibilité au code source et des droits de copie,
de modification et de redistribution accordés par cette licence, il n'est
offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
seule une responsabilité restreinte pèse sur l'auteur du programme,  le
titulaire des droits patrimoniaux et les concédants successifs.

A cet égard  l'attention de l'utilisateur est attirée sur les risques
associés au chargement,  à l'utilisation,  à la modification et/ou au
développement et à la reproduction du logiciel par l'utilisateur étant
donné sa spécificité de logiciel libre, qui peut le rendre complexe à
manipuler et qui le réserve donc à des développeurs et des professionnels
avertis possédant  des  connaissances  informatiques approfondies.  Les
utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
logiciel à leurs besoins dans des conditions permettant d'assurer la
sécurité de leurs systèmes et ou de leurs données et, plus généralement,
à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.

Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
termes.