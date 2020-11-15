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
use App\Entity\Traits\TimeTrait;
use App\Enum\ArrivalOrDeparture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class TravelGroup extends BaseEntity
{
    use IdTrait;
    use TimeTrait;

    /**
     * @var int
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="integer")
     */
    private $arrivalOrDeparture = ArrivalOrDeparture::ARRIVAL;

    /**
     * @var string
     *
     * @Groups({"travel-export"})
     * @ORM\Column(type="string")
     */
    private $location;

    /**
     * @var \DateTime
     *
     * @Groups({"travel-export"})
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    /**
     * @var string
     *
     * @Groups({"travel-export"})
     * @ORM\Column(type="string")
     */
    private $identifier;

    /**
     * @var string|null
     *
     * @Groups({"travel-export"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $details;

    /**
     * @var Delegation
     *
     * @Groups({"travel-export"})
     * @ORM\ManyToOne (targetEntity="App\Entity\Delegation", inversedBy="travelGroups")
     */
    private $delegation;

    /**
     * @var Participant[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant", inversedBy="travelGroups")
     * @ORM\OrderBy({"role" = "ASC", "givenName" = "ASC"})
     */
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getArrivalOrDeparture(): int
    {
        return $this->arrivalOrDeparture;
    }

    public function setArrivalOrDeparture(int $arrivalOrDeparture): void
    {
        $this->arrivalOrDeparture = $arrivalOrDeparture;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): void
    {
        $this->details = $details;
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
     * @return Participant[]|ArrayCollection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param Participant[]|ArrayCollection $participants
     */
    public function setParticipants($participants): void
    {
        $this->participants = $participants;
    }
}
