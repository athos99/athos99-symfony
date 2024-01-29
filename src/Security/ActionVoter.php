<?php

namespace App\Security;


use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ActionVoter extends Voter
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    protected const AUTHORIZED = [

        Action::HOME => [Role::ALL],
        Action::DEBUG => [Role::ADMINISTRATEUR],
        Action::DEBUG_DB => [Role::ADMINISTRATEUR],
        Action::DEBUG_JOB => [Role::ADMINISTRATEUR],
        Action::DEBUG_LOG => [Role::ADMINISTRATEUR],
        Action::DEBUG_DIAGNOSTIQUE => [Role::ADMINISTRATEUR,Role::UTILISATEUR],
        Action::SONDE_DIAGNOSTIQUE => [Role::ADMINISTRATEUR,Role::UTILISATEUR],
        Action::SETTINGS => [Role::ADMIN_SITE, Role::ADMINISTRATEUR,],

        // parameter
        Action::PARAMETRE_READ => [ Role::ADMINISTRATEUR, Role::ADMIN_SITE],
        Action::PARAMETRE_WRITE => [ Role::ADMINISTRATEUR],

        //Consultation détail d'un avis
        Action::CONSULTER_ADMIN => [Role::ADMIN_SITE, Role::ADMINISTRATEUR],
    ];


    /**
     * Indique si une action (attribute) est géré par ce voter
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject) :bool
    {
        return in_array($attribute, Action::getActions(), true);
    }


    /**
     * Indique si le role donne droit a effectuer une action
     *
     * @param string $attribute
     * @param mixed $post
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }
        $authorizeds = self::AUTHORIZED[$attribute] ?? [];
        foreach ($authorizeds as $authorized) {
            if ($this->security->isGranted($authorized)) {
                return true;
            }
        }
        return false;
    }

}
