<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Traits;

use App\Enum\ParticipantMode;
use App\Enum\ReviewProgress;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait DelegationAttendanceTrait
{
    /**
     * @var int
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="integer")
     */
    private $contestantCount = 0;

    /**
     * @var int
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="integer")
     */
    private $leaderCount = 0;

    /**
     * @var int
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="integer")
     */
    private $guestCount = 0;

    /**
     * @var int
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="integer")
     */
    private $participationMode = ParticipantMode::ONSITE;

    /**
     * @var int
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="integer")
     */
    private $attendanceReviewProgress = ReviewProgress::NOT_EDITED;

    public function getContestantCount(): int
    {
        return $this->contestantCount;
    }

    public function setContestantCount(int $contestantCount): void
    {
        $this->contestantCount = $contestantCount;
    }

    public function getLeaderCount(): int
    {
        return $this->leaderCount;
    }

    public function setLeaderCount(int $leaderCount): void
    {
        $this->leaderCount = $leaderCount;
    }

    public function getGuestCount(): int
    {
        return $this->guestCount;
    }

    public function setGuestCount(int $guestCount): void
    {
        $this->guestCount = $guestCount;
    }

    public function getParticipationMode(): int
    {
        return $this->participationMode;
    }

    public function setParticipationMode(int $participationMode): void
    {
        $this->participationMode = $participationMode;
    }

    public function getAttendanceReviewProgress(): int
    {
        return $this->attendanceReviewProgress;
    }

    public function setAttendanceReviewProgress(int $attendanceReviewProgress): void
    {
        $this->attendanceReviewProgress = $attendanceReviewProgress;
    }

    public function expectedAttendance()
    {
        return $this->leaderCount + $this->contestantCount + $this->guestCount;
    }

    public function isAttendanceComplete()
    {
        return $this->expectedAttendance() > 0;
    }
}
