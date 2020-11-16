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
use App\Enum\ArrivalOrDeparture;
use App\Enum\ReviewProgress;
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
     * @Groups({"delegation-export", "participant-export", "travel-group-export"})
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
     * @ORM\OrderBy({"arrivalOrDeparture" = "ASC", "dateTime" = "ASC"})
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

    public function getParticipantWithRole(int $role, int $offset = 0): ?Participant
    {
        foreach ($this->participants as $participant) {
            if ($participant->getRole() === $role && 0 === $offset--) {
                return $participant;
            }
        }

        return null;
    }

    /**
     * @return TravelGroup[]
     */
    public function getTravelGroupsByArrivalOrDeparture(int $arrivalOrDeparture): array
    {
        $travelGroups = [];
        foreach ($this->travelGroups as $travelGroup) {
            if ($travelGroup->getArrivalOrDeparture() === $arrivalOrDeparture) {
                $travelGroups[] = $travelGroup;
            }
        }

        return $travelGroups;
    }

    /**
     * @return Participant[]
     */
    public function getParticipantsWithoutTravelGroup(int $arrivalOrDeparture): array
    {
        $participants = [];
        foreach ($this->getParticipants() as $participant) {
            if (ArrivalOrDeparture::ARRIVAL === $arrivalOrDeparture) {
                if (null === $participant->getArrivalTravelGroup()) {
                    $participants[] = $participant;
                }
            } else {
                if (null === $participant->getDepartureTravelGroup()) {
                    $participants[] = $participant;
                }
            }
        }

        return $participants;
    }

    public function missingParticipants()
    {
        return max(0, $this->expectedAttendance() - count($this->getParticipants()));
    }

    public function getParticipantReviewProgress()
    {
        $chapter = $this->summarizeParticipants(
            function (Participant $participant) {
                return $participant->getPersonalDataReviewProgress();
            },
            function (Participant $participant) {
                return $participant->isPersonalDataComplete();
            }
        );
        $summary['personal_data'] = $chapter;

        $chapter = $this->summarizeParticipants(
            function (Participant $participant) {
                return $participant->getImmigrationReviewProgress();
            },
            function (Participant $participant) {
                return $participant->isImmigrationComplete();
            }
        );
        $summary['immigration'] = $chapter;

        $chapter = $this->summarizeParticipants(
            function (Participant $participant) {
                return $participant->getEventPresenceReviewProgress();
            },
            function (Participant $participant) {
                return $participant->isEventPresenceComplete();
            }
        );
        $summary['onsite'] = $chapter;

        return $summary;
    }

    public function getTravelGroupReviewProgress()
    {
        $chapter = $this->summarizeTravelGroup(
            function (Participant $participant) {
                return $participant->getArrivalTravelGroup();
            }
        );
        $summary['arrival'] = $chapter;

        $chapter = $this->summarizeTravelGroup(
            function (Participant $participant) {
                return $participant->getDepartureTravelGroup();
            }
        );
        $summary['departure'] = $chapter;

        return $summary;
    }

    private function summarizeTravelGroup(callable $getTravelGroup)
    {
        return $this->summarizeParticipants(
            function (Participant $participant) use ($getTravelGroup) {
                return $getTravelGroup($participant) ? $getTravelGroup($participant)->getReviewProgress() : ReviewProgress::NOT_EDITED;
            },
            function (Participant $participant) use ($getTravelGroup) {
                return $getTravelGroup($participant) ? $getTravelGroup($participant)->complete() : false;
            }
        );
    }

    private function summarizeParticipants(callable $getReviewProgress, callable $getIsComplete)
    {
        $chapter = ['data_missing' => $this->expectedAttendance(), 'pending_review' => 0, 'reviewed' => 0];
        foreach ($this->participants as $participant) {
            if (ReviewProgress::REVIEWED_AND_LOCKED === $getReviewProgress($participant)) {
                --$chapter['data_missing'];
                ++$chapter['reviewed'];
            } elseif ($getIsComplete($participant)) {
                --$chapter['data_missing'];
                ++$chapter['pending_review'];
            }
        }

        return $chapter;
    }
}
