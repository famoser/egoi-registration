<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\ParticipantTraits;

use App\Enum\Diet;
use App\Enum\ReviewProgress;
use Doctrine\ORM\Mapping as ORM;

trait EventDataTrait
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
    private $eventDataReviewProgress = ReviewProgress::NOT_EDITED;

    /**
     * @return string|null
     */
    public function getBadeName(): ?string
    {
        return $this->badeName;
    }

    /**
     * @param string|null $badeName
     */
    public function setBadeName(?string $badeName): void
    {
        $this->badeName = $badeName;
    }

    /**
     * @return string|null
     */
    public function getBadeImage(): ?string
    {
        return $this->badeImage;
    }

    /**
     * @param string|null $badeImage
     */
    public function setBadeImage(?string $badeImage): void
    {
        $this->badeImage = $badeImage;
    }

    /**
     * @return string|null
     */
    public function getDiet(): ?string
    {
        return $this->diet;
    }

    /**
     * @param string|null $diet
     */
    public function setDiet(?string $diet): void
    {
        $this->diet = $diet;
    }

    /**
     * @return string|null
     */
    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    /**
     * @param string|null $allergies
     */
    public function setAllergies(?string $allergies): void
    {
        $this->allergies = $allergies;
    }

    /**
     * @return int
     */
    public function getEventDataReviewProgress(): int
    {
        return $this->eventDataReviewProgress;
    }

    /**
     * @param int $eventDataReviewProgress
     */
    public function setEventDataReviewProgress(int $eventDataReviewProgress): void
    {
        $this->eventDataReviewProgress = $eventDataReviewProgress;
    }

    public function isEventDataComplete()
    {
        return !empty($this->badeName) &&
            !empty($this->badeImage) &&
            !empty($this->diet) &&
            !empty($this->allergies);
    }
}
