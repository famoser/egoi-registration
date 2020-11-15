<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\TravelGroup;
use App\Enum\ArrivalOrDeparture;
use Doctrine\ORM\EntityRepository;

class ParticipantRepository extends EntityRepository
{
    public function createQueryBuilderForEligibleParticipants(TravelGroup $travelGroup)
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.role', 'ASC')
            ->orderBy('p.givenName', 'ASC')
            ->where('p.delegation = :delegation')
            ->setParameter(':delegation', $travelGroup->getDelegation()->getId());

        if (ArrivalOrDeparture::ARRIVAL === $travelGroup->getArrivalOrDeparture()) {
            $qb->andWhere('(p.arrivalTravelGroup IS NULL OR p.arrivalTravelGroup = :travelGroup)');
        } else {
            $qb->andWhere('(p.departureTravelGroup IS NULL OR p.departureTravelGroup = :travelGroup)');
        }

        $qb->setParameter(':travelGroup', $travelGroup->getId());

        return $qb;
    }
}
