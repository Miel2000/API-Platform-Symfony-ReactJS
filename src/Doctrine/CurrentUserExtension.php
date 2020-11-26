<?php 

namespace App\Doctrine;

use App\Entity\Invoice;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\Customer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface 
{

    private $security;
    private $auth;

    public function __construct(Security $security, AuthorizationCheckerInterface $checker){
            $this->security = $security;
            $this->auth = $checker;
    }


    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass){
        // 1 obtenir l'utilisateur connecté

        $user = $this->security->getUser();

        if (
           ( $resourceClass === Invoice::class || $resourceClass === Customer::class) &&
           !$this->auth->isGranted('ROLE_ADMIN') &&
           $user instanceof User
        ) {
            // choper le rootAlias de la requet SQL (visible via dd($queryBuilder))
            $rootAlias = $queryBuilder->getRootAliases()[0];

            // on select la table custom
            if ($resourceClass === Customer::class) {


                $queryBuilder->andWhere("$rootAlias.user = :user");
                // on where user

            }

            // on select la table Invoice
            else if ($resourceClass === Invoice::class) {

                // on la join aux customers
                $queryBuilder->join("$rootAlias.customer", "c");
                // on where $user :user
                $queryBuilder->andWhere("c.user = :user");
            }
            // dans tout les cas on veut binder $user en parameter
            $queryBuilder->setParameter("user", $user);
        }

        // 2 Si on demande des invoices oud es customers, alors agir sur la requete pour qu'elle tienne compte de l'utilisateur connecté


    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null)
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    
    
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass, 
        array $identifiers,
        string $operationName = null,
        array $context = [])
    {
        
        $this->addWhere($queryBuilder, $resourceClass);
    }

}