<?php

namespace App\Security\Voter;

use App\Entity\Products;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter
{
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // deux méthodes obligatoires pour extends Voter

    protected function supports(string $attribute, $product): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        if (!$product instanceof Products) {
            return false;
        }
        return true;

        // on aurait pu écrire en une seule ligne : 
        // return in_array($attribute, [self::EDIT, self::DELETE]) && $product instanceof Products;
    }   

    protected function voteOnAttribute($attribute, $product, TokenInterface $token): bool 
    {
        // on récupère l'utilisateur à partir du token
        $user = $token->getUser();

        // on vérifie si l'utilisateur est connecté
        if (!$user instanceof UserInterface) {
            return false;
        }

        // on vérifie si l'utilisateur est admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // on pourrait aussi bien écrire
        // if($this->security->isGranted('ROLE_ADMIN')) return true;

        // on vérifie les permissions de l'utilisateur connecté
        switch($attribute) {
            case self::EDIT:
                // on vérifie si l'utilisateur peut éditer
                return $this->canEdit();
                break;
            case self::DELETE:
                // on vérifie si l'utilisateur peut supprimer
                return $this->canDelete();
                break;
        }
    }

    // il est conseillé de mettre les méthodes dans des fonctions à part

    private function canEdit() {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }

    private function canDelete() {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
}
