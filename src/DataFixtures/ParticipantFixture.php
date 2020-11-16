<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Delegation;
use App\Entity\Participant;
use App\Enum\ParticipantRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ParticipantFixture extends Fixture implements OrderedFixtureInterface
{
    const ORDER = DelegationFixture::ORDER + 1;

    public function load(ObjectManager $manager)
    {
        $participants = [
            [ParticipantRole::LEADER, 'Peter', 'McLead'],
            [ParticipantRole::DEPUTY_LEADER, 'Markus', 'McDeputyLead'],
            [ParticipantRole::CONTESTANT, 'William', 'McContestant'],
            [ParticipantRole::CONTESTANT, 'Williem', 'McContestant'],
            [ParticipantRole::CONTESTANT, 'Williom', 'McContestant'],
            [ParticipantRole::CONTESTANT, 'Willium', 'McContestant'],
            [ParticipantRole::GUEST, 'Klaus', 'McGues'],
            [ParticipantRole::GUEST, 'Klous', 'McGues'],
        ];

        /** @var Delegation $delegation */
        $delegation = $this->getReference(DelegationFixture::DELEGATION_SWISS);
        $delegation->setContestantCount(4);
        $delegation->setLeaderCount(2);
        $delegation->setGuestCount(3);
        $manager->persist($delegation);

        foreach ($participants as $entry) {
            $participant = new Participant();
            $participant->setRole($entry[0]);
            $participant->setGivenName($entry[1]);
            $participant->setFamilyName($entry[2]);
            $participant->setDelegation($delegation);

            $manager->persist($participant);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return self::ORDER;
    }
}
