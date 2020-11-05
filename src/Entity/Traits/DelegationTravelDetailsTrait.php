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

use App\Enum\ReviewProgress;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait DelegationTravelDetailsTrait
{
    /**
     * @var string|null
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $location;

    /**
     * @var string|null
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $travelDetails;

    /**
     * @var int
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="integer")
     */
    private $travelReviewProgress = ReviewProgress::NOT_EDITED;

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getTravelDetails(): ?string
    {
        return $this->travelDetails;
    }

    public function setTravelDetails(?string $travelDetails): void
    {
        $this->travelDetails = $travelDetails;
    }

    public function getTravelReviewProgress(): int
    {
        return $this->travelReviewProgress;
    }

    public function setTravelReviewProgress(int $travelReviewProgress): void
    {
        $this->travelReviewProgress = $travelReviewProgress;
    }

    public function isTravelComplete()
    {
        return !empty($this->location) &&
            !empty($this->travelDetails);
    }
}
