<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\DataFixtures;

use App\Entity\Participant;
use App\Entity\ConstructionSite;
use App\Service\Interfaces\SampleServiceInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TestConstructionSiteFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @var SampleServiceInterface
     */
    private $sampleService;

    public const ORDER = TestConstructionManagerFixtures::ORDER + 1;
    public const TEST_CONSTRUCTION_SITE_NAME = SampleServiceInterface::TEST;
    public const EMPTY_CONSTRUCTION_SITE_NAME = 'empty';

    /**
     * TestConstructionSiteFixtures constructor.
     */
    public function __construct(SampleServiceInterface $sampleService)
    {
        $this->sampleService = $sampleService;
    }

    public function load(ObjectManager $manager)
    {
        $constructionManagerRepository = $manager->getRepository(Participant::class);

        /** @var Participant $constructionManager */
        $constructionManager = $constructionManagerRepository->findOneBy(['email' => TestConstructionManagerFixtures::CONSTRUCTION_MANAGER_EMAIL]);
        $constructionSite = $this->createAndAssignSampleConstructionSite($constructionManager);
        $manager->persist($constructionSite);
        $manager->persist($constructionManager);

        /** @var Participant $constructionManager2 */
        $constructionManager2 = $constructionManagerRepository->findOneBy(['email' => TestConstructionManagerFixtures::CONSTRUCTION_MANAGER_2_EMAIL]);
        $constructionSite->getConstructionManagers()->add($constructionManager2);
        $constructionManager2->getConstructionSites()->add($constructionSite);
        $manager->persist($constructionManager2);

        $constructionSite = $this->createEmptyConstructionSite();
        $manager->persist($constructionSite);

        $manager->flush();
    }

    public function getOrder()
    {
        return self::ORDER;
    }

    private function createAndAssignSampleConstructionSite(?Participant $testUser): ConstructionSite
    {
        $constructionSite = $this->sampleService->createSampleConstructionSite(self::TEST_CONSTRUCTION_SITE_NAME, $testUser);

        $constructionSite->getConstructionManagers()->add($testUser);
        $testUser->getConstructionSites()->add($constructionSite);

        return $constructionSite;
    }

    private function createEmptyConstructionSite(): ConstructionSite
    {
        $constructionSite = new ConstructionSite();
        $constructionSite->setName(self::EMPTY_CONSTRUCTION_SITE_NAME);
        $constructionSite->setFolderName(self::EMPTY_CONSTRUCTION_SITE_NAME);
        $constructionSite->setStreetAddress('Street');
        $constructionSite->setPostalCode(4123);
        $constructionSite->setLocality('Allschwil');
        $constructionSite->setCountry('CH');

        return $constructionSite;
    }
}
