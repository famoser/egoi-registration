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

trait DelegationTravelDetailsTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $travelDetails;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $travelDetailsReviewProgress = ReviewProgress::NOT_EDITED;

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

    public function getTravelDetailsReviewProgress(): int
    {
        return $this->travelDetailsReviewProgress;
    }

    public function setTravelDetailsReviewProgress(int $travelDetailsReviewProgress): void
    {
        $this->travelDetailsReviewProgress = $travelDetailsReviewProgress;
    }

    public function isDelegationTravelDataComplete()
    {
        return !empty($this->location) &&
            !empty($this->travelDetails);
    }
}
