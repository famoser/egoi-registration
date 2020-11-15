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
use Symfony\Component\Serializer\Annotation\Groups;

trait ParticipantEventPresenceTrait
{
    /**
     * @var string|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $diet = Diet::NONE;

    /**
     * @var string|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $allergies;

    /**
     * @var int
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="integer")
     */
    private $eventPresenceReviewProgress = ReviewProgress::NOT_EDITED;

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

    public function getEventPresenceReviewProgress(): int
    {
        return $this->eventPresenceReviewProgress;
    }

    public function setEventPresenceReviewProgress(int $eventPresenceReviewProgress): void
    {
        $this->eventPresenceReviewProgress = $eventPresenceReviewProgress;
    }

    public function isEventPresenceComplete()
    {
        return !empty($this->badgeName) &&
            !empty($this->badgeImage) &&
            !empty($this->diet) &&
            !empty($this->allergies);
    }
}
