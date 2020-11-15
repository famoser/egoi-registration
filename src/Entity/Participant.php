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
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\ParticipantEventPresenceTrait;
use App\Entity\Traits\ParticipantImmigrationTrait;
use App\Entity\Traits\ParticipantPersonalDataTrait;
use App\Entity\Traits\TimeTrait;
use App\Enum\ParticipantRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Participant extends BaseEntity
{
    use IdTrait;
    use TimeTrait;

    use ParticipantPersonalDataTrait;
    use ParticipantImmigrationTrait;
    use ParticipantEventPresenceTrait;

    /**
     * @var int
     *
     * @Groups({"participant-export", "travel-export"})
     * @ORM\Column(type="integer")
     */
    private $role = ParticipantRole::CONTESTANT;

    /**
     * @var Delegation
     *
     * @Groups({"participant-export"})
     * @ORM\ManyToOne(targetEntity="Delegation", inversedBy="participants")
     */
    private $delegation;

    /**
     * @var TravelGroup[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\TravelGroup", mappedBy="participants")
     * @ORM\OrderBy({"arrivalOrDeparture" = "ASC"})
     */
    private $travelGroups;

    public function __construct()
    {
        $this->travelGroups = new ArrayCollection();
    }

    public function getRole(): int
    {
        return $this->role;
    }

    public function setRole(int $role): void
    {
        $this->role = $role;
    }

    public function getDelegation(): Delegation
    {
        return $this->delegation;
    }

    public function setDelegation(Delegation $delegation): void
    {
        $this->delegation = $delegation;
    }

    /**
     * @return TravelGroup[]|ArrayCollection
     */
    public function getTravelGroups()
    {
        return $this->travelGroups;
    }
}
