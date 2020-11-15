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
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DelegationFixture extends Fixture implements OrderedFixtureInterface
{
    const ORDER = 0;

    public const DELEGATION_SWISS = 'DELEGATION_SWISS';

    public function load(ObjectManager $manager)
    {
        $entries = [
            ['Germany'],
            ['Great Britain'],
            ['Switzerland'],
        ];

        foreach ($entries as $entry) {
            $delegation = new Delegation();
            $delegation->setName($entry[0]);
            $manager->persist($delegation);
        }

        $this->addReference(self::DELEGATION_SWISS, $delegation);

        $manager->flush();
    }

    public function getOrder()
    {
        return self::ORDER;
    }
}
