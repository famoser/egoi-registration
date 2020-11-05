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

use App\Enum\Diet;
use App\Enum\ReviewProgress;
use Doctrine\ORM\Mapping as ORM;

trait ParticipantEventAttendanceTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $badeName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $badeImage;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $diet = Diet::NONE;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $allergies;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $eventAttendanceReviewProgress = ReviewProgress::NOT_EDITED;

    public function getBadeName(): ?string
    {
        return $this->badeName;
    }

    public function setBadeName(?string $badeName): void
    {
        $this->badeName = $badeName;
    }

    public function getBadeImage(): ?string
    {
        return $this->badeImage;
    }

    public function setBadeImage(?string $badeImage): void
    {
        $this->badeImage = $badeImage;
    }

    public function getDiet(): ?string
    {
        return $this->diet;
    }

    public function setDiet(?string $diet): void
    {
        $this->diet = $diet;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): void
    {
        $this->allergies = $allergies;
    }

    public function getEventAttendanceReviewProgress(): int
    {
        return $this->eventAttendanceReviewProgress;
    }

    public function setEventAttendanceReviewProgress(int $eventAttendanceReviewProgress): void
    {
        $this->eventAttendanceReviewProgress = $eventAttendanceReviewProgress;
    }

    public function isEventDataComplete()
    {
        return !empty($this->badeName) &&
            !empty($this->badeImage) &&
            !empty($this->diet) &&
            !empty($this->allergies);
    }
}
