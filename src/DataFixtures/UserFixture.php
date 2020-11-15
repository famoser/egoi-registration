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
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture implements OrderedFixtureInterface
{
    const ORDER = DelegationFixture::ORDER + 1;

    public function load(ObjectManager $manager)
    {
        $admins = [
            ['f@egoi.org', 'asdf'],
        ];

        foreach ($admins as $entry) {
            $user = new User();
            $user->setEmail($entry[0]);
            $user->setPasswordFromPlain($entry[1]);
            $user->setIsEnabled(true);
            $user->setIsAdmin(true);
            $manager->persist($user);
        }

        /** @var Delegation $delegation */
        $delegation = $this->getReference(DelegationFixture::DELEGATION_SWISS);
        $delegationUsers = [
            ['ch@egoi.org', 'asdf'],
            ['ch_secondary@egoi.org', 'asdf'],
        ];
        for ($i = 0; $i < count($delegationUsers); ++$i) {
            $user = new User();
            $user->setEmail($delegationUsers[$i][0]);
            $user->setPasswordFromPlain($delegationUsers[$i][1]);
            $user->setIsEnabled(true);

            $user->setDelegation($delegation);
            $delegation->getUsers()->add($user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return self::ORDER;
    }
}
