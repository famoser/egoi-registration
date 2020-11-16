<?php

/*
 * This file is part of the famoser/egoi-registration project.
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
use App\Enum\ReviewProgress;
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
     * @Groups({"travel-group-export"})
     * @ORM\Column(type="integer")
     */
    private $arrivalOrDeparture = ArrivalOrDeparture::ARRIVAL;

    /**
     * @var string|null
     *
     * @Groups({"travel-group-export"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $location;

    /**
     * @var \DateTime|null
     *
     * @Groups({"travel-group-export"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateTime;

    /**
     * @var string|null
     *
     * @Groups({"travel-group-export"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $provider;

    /**
     * @var string|null
     *
     * @Groups({"travel-group-export"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $tripNumber;

    /**
     * @var string|null
     *
     * @Groups({"travel-group-export"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @Groups({"travel-group-export"})
     * @ORM\Column(type="integer")
     */
    private $reviewProgress = ReviewProgress::NOT_EDITED;

    /**
     * @var Delegation
     *
     * @Groups({"travel-group-export"})
     * @ORM\ManyToOne (targetEntity="App\Entity\Delegation", inversedBy="travelGroups")
     */
    private $delegation;

    /**
     * @var Participant[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="arrivalTravelGroup")
     * @ORM\OrderBy({"role" = "ASC", "givenName" = "ASC"})
     */
    private $arrivalParticipants;

    /**
     * @var Participant[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="departureTravelGroup")
     * @ORM\OrderBy({"role" = "ASC", "givenName" = "ASC"})
     */
    private $departureParticipants;

    public function __construct()
    {
        $this->arrivalParticipants = new ArrayCollection();
        $this->departureParticipants = new ArrayCollection();
    }

    public function getArrivalOrDeparture(): int
    {
        return $this->arrivalOrDeparture;
    }

    public function setArrivalOrDeparture(int $arrivalOrDeparture): void
    {
        $this->arrivalOrDeparture = $arrivalOrDeparture;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): void
    {
        $this->provider = $provider;
    }

    public function getTripNumber(): ?string
    {
        return $this->tripNumber;
    }

    public function setTripNumber(?string $tripNumber): void
    {
        $this->tripNumber = $tripNumber;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDelegation(): Delegation
    {
        return $this->delegation;
    }

    public function setDelegation(Delegation $delegation): void
    {
        $this->delegation = $delegation;
    }

    public function getParticipants()
    {
        return ArrivalOrDeparture::ARRIVAL === $this->arrivalOrDeparture ? $this->getArrivalParticipants() : $this->getDepartureParticipants();
    }

    public function addParticipant(Participant $participant)
    {
        if (ArrivalOrDeparture::ARRIVAL === $this->arrivalOrDeparture) {
            $this->arrivalParticipants->add($participant);
            $participant->setArrivalTravelGroup($this);
        } else {
            $this->departureParticipants->add($participant);
            $participant->setDepartureTravelGroup($this);
        }
    }

    public function removeParticipant($participant)
    {
        if (ArrivalOrDeparture::ARRIVAL === $this->arrivalOrDeparture) {
            $this->arrivalParticipants->removeElement($participant);
            $participant->setArrivalTravelGroup(null);
        } else {
            $this->departureParticipants->removeElement($participant);
            $participant->setDepartureTravelGroup(null);
        }
    }

    /**
     * @Groups({"travel-group-export"})
     */
    public function getParticipantCount()
    {
        return count($this->getParticipants());
    }

    /**
     * @return Participant[]|ArrayCollection
     */
    public function getArrivalParticipants()
    {
        return $this->arrivalParticipants;
    }

    /**
     * @return Participant[]|ArrayCollection
     */
    public function getDepartureParticipants()
    {
        return $this->departureParticipants;
    }

    public function getReviewProgress(): int
    {
        return $this->reviewProgress;
    }

    public function setReviewProgress(int $reviewProgress): void
    {
        $this->reviewProgress = $reviewProgress;
    }

    public function complete()
    {
        return !empty($this->location) &&
            !empty($this->dateTime) &&
            !empty($this->provider) &&
            !empty($this->tripNumber);
    }
}
