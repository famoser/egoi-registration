<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Traits\DelegationAttendanceTrait;
use App\Entity\Traits\DelegationContributionTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TimeTrait;
use App\Helper\HashHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Delegation extends BaseEntity
{
    use IdTrait;
    use TimeTrait;

    use DelegationAttendanceTrait;
    use DelegationContributionTrait;

    /**
     * @var string
     *
     * @Groups({"delegation-export", "participant-export"})
     * @ORM\Column(type="text", unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $registrationHash;

    /**
     * @var User[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="delegation")
     * @ORM\OrderBy({"email" = "ASC"})
     */
    private $users;

    /**
     * @var Participant[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="delegation")
     * @ORM\OrderBy({"role" = "ASC", "givenName" = "ASC"})
     */
    private $participants;

    /**
     * @var TravelGroup[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\TravelGroup", mappedBy="delegation")
     */
    private $travelGroups;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->travelGroups = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRegistrationHash(): string
    {
        return $this->registrationHash;
    }

    /**
     * @ORM\PrePersist()
     */
    public function generateRegistrationHash()
    {
        $this->registrationHash = HashHelper::getHash();
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return Participant[]|ArrayCollection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @return TravelGroup[]|ArrayCollection
     */
    public function getTravelGroups()
    {
        return $this->travelGroups;
    }
}
