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

trait DelegationAttendanceTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $athleteCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $leaderCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $guestCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $participationMode = ParticipantMode::ONSITE;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $attendanceReviewProgress = ReviewProgress::NOT_EDITED;

    public function getAthleteCount(): int
    {
        return $this->athleteCount;
    }

    public function setAthleteCount(int $athleteCount): void
    {
        $this->athleteCount = $athleteCount;
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

    public function isAttendanceComplete()
    {
        return $this->leaderCount + $this->athleteCount + $this->guestCount > 0;
    }
}
