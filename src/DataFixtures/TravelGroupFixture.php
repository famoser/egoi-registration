<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Delegation;
use App\Entity\TravelGroup;
use App\Enum\ArrivalOrDeparture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TravelGroupFixture extends Fixture implements OrderedFixtureInterface
{
    const ORDER = ParticipantFixture::ORDER + 1;

    public function load(ObjectManager $manager)
    {
        $travelGroups = [
            [ArrivalOrDeparture::ARRIVAL, 'Zürich HB', new \DateTime('today 14:00'), 'SBB', 'ICE 125'],
        ];

        /** @var Delegation $delegation */
        $delegation = $this->getReference(DelegationFixture::DELEGATION_SWISS);

        foreach ($travelGroups as $entry) {
            $travelGroup = new TravelGroup();
            $travelGroup->setArrivalOrDeparture($entry[0]);
            $travelGroup->setLocation($entry[1]);
            $travelGroup->setDateTime($entry[2]);
            $travelGroup->setProvider($entry[3]);
            $travelGroup->setTripNumber($entry[4]);

            $travelGroup->setDelegation($delegation);
            foreach ($delegation->getParticipants() as $participant) {
                $travelGroup->addParticipant($participant);
            }

            $manager->persist($travelGroup);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return self::ORDER;
    }
}
