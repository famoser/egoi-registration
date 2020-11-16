<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security\Voter;

use App\Entity\Delegation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DelegationVoter extends Voter
{
    const DELEGATION_VIEW = 'DELEGATION_VIEW';
    const DELEGATION_EDIT = 'DELEGATION_EDIT';
    const DELEGATION_MODERATE = 'DELEGATION_MODERATE';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string     $attribute An attribute
     * @param Delegation $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (self::DELEGATION_MODERATE === $attribute) {
            return true;
        }

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELEGATION_VIEW, self::DELEGATION_EDIT, self::DELEGATION_MODERATE])) {
            return false;
        }

        return $subject instanceof Delegation;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string     $attribute
     * @param Delegation $subject
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($user instanceof User) {
            $userIsAdmin = in_array(User::ROLE_ADMIN, $user->getRoles());

            if ($userIsAdmin) {
                return true;
            }

            return in_array($attribute, [self::DELEGATION_VIEW, self::DELEGATION_EDIT]) && $user->getDelegation() === $subject;
        }

        throw new \LogicException('Unknown user payload '.serialize($user).'!');
    }
}
