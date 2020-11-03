<?php

/*
 * This file is part of the mangel.io project.
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

class CountryVoter extends Voter
{
    const DELEGATION_VIEW = 'DELEGATION_VIEW';
    const DELEGATION_EDIT = 'DELEGATION_EDIT';

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
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELEGATION_VIEW, self::DELEGATION_EDIT])) {
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
            if (in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return true;
            }

            return $user->getDelegation() === $subject;
        }

        throw new \LogicException('Unknown user type '.get_class($user).'!');
    }
}
