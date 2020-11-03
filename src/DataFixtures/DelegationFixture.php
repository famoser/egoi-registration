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

    public function load(ObjectManager $manager)
    {
        $entries = [
            ['CH'],
            ['DE'],
            ['GB'],
        ];

        foreach ($entries as $entry) {
            $user = new Delegation();
            $user->setName($entry[0]);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return self::ORDER;
    }
}
