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
use App\Entity\User;
use App\Enum\ReviewProgress;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ParticipantVoter extends Voter
{
    const PARTICIPANT_VIEW = 'PARTICIPANT_VIEW';
    const PARTICIPANT_EDIT = 'PARTICIPANT_EDIT';
    const PARTICIPANT_REMOVE = 'PARTICIPANT_REMOVE';
    const PARTICIPANT_MODERATE = 'PARTICIPANT_MODERATE';

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
        if (self::PARTICIPANT_MODERATE === $attribute) {
            return true;
        }

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::PARTICIPANT_EDIT, self::PARTICIPANT_REMOVE, self::PARTICIPANT_MODERATE])) {
            return false;
        }

        return $subject instanceof Participant;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string      $attribute
     * @param Participant $subject
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

            if ($user->getDelegation() !== $subject->getDelegation()) {
                return false;
            }

            if (self::PARTICIPANT_REMOVE === $attribute) {
                return ReviewProgress::REVIEWED_AND_LOCKED !== $subject->getPersonalDataReviewProgress();
            }

            return self::PARTICIPANT_EDIT === $attribute;
        }

        throw new \LogicException('Unknown user payload '.serialize($user).'!');
    }
}
