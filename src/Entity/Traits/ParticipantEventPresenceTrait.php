<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Traits;

use App\Enum\Diet;
use App\Enum\ReviewProgress;
use App\Enum\ShirtFit;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ParticipantEventPresenceTrait
{
    /**
     * @var int|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shirtSize;

    /**
     * @var int|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shirtFit = ShirtFit::FEMALE;

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
     * @var bool|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $singleRoom;

    /**
     * @var int
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="integer")
     */
    private $eventPresenceReviewProgress = ReviewProgress::NOT_EDITED;

    public function getShirtSize(): ?int
    {
        return $this->shirtSize;
    }

    public function setShirtSize(?int $shirtSize): void
    {
        $this->shirtSize = $shirtSize;
    }

    public function getShirtFit(): ?int
    {
        return $this->shirtFit;
    }

    public function setShirtFit(?int $shirtFit): void
    {
        $this->shirtFit = $shirtFit;
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

    public function getEventPresenceReviewProgress(): int
    {
        return $this->eventPresenceReviewProgress;
    }

    public function setEventPresenceReviewProgress(int $eventPresenceReviewProgress): void
    {
        $this->eventPresenceReviewProgress = $eventPresenceReviewProgress;
    }

    public function getSingleRoom(): ?bool
    {
        return $this->singleRoom;
    }

    public function setSingleRoom(?bool $singleRoom): void
    {
        $this->singleRoom = $singleRoom;
    }

    abstract public function isLeader(): bool;

    public function isEventPresenceComplete()
    {
        $validation = null !== $this->shirtSize &&
            null !== $this->shirtFit &&
            null !== $this->diet;

        if ($this->isLeader()) {
            $validation &= null !== $this->singleRoom;
        }

        return $validation;
    }
}
