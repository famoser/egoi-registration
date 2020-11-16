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

use App\Entity\Participant;
use App\Entity\TravelGroup;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TravelGroupVoter extends Voter
{
    const TRAVEL_GROUP_EDIT = 'TRAVEL_GROUP_EDIT';
    const TRAVEL_GROUP_MODERATE = 'TRAVEL_GROUP_MODERATE';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string      $attribute An attribute
     * @param Participant $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (self::TRAVEL_GROUP_MODERATE === $attribute) {
            return true;
        }

        return self::TRAVEL_GROUP_EDIT === $attribute && $subject instanceof TravelGroup;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string      $attribute
     * @param TravelGroup $subject
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

            return self::TRAVEL_GROUP_EDIT === $attribute && $user->getDelegation() === $subject->getDelegation();
        }

        throw new \LogicException('Unknown user payload '.serialize($user).'!');
    }
}
