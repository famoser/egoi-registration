<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\DelegationTraits;

use App\Enum\Diet;
use App\Enum\ReviewProgress;
use Doctrine\ORM\Mapping as ORM;

trait TravelDataTrait
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
    private $travelDataReviewProgress = ReviewProgress::NOT_EDITED;

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     */
    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string|null
     */
    public function getTravelDetails(): ?string
    {
        return $this->travelDetails;
    }

    /**
     * @param string|null $travelDetails
     */
    public function setTravelDetails(?string $travelDetails): void
    {
        $this->travelDetails = $travelDetails;
    }

    /**
     * @return int
     */
    public function getTravelDataReviewProgress(): int
    {
        return $this->travelDataReviewProgress;
    }

    /**
     * @param int $travelDataReviewProgress
     */
    public function setTravelDataReviewProgress(int $travelDataReviewProgress): void
    {
        $this->travelDataReviewProgress = $travelDataReviewProgress;
    }

    public function isDelegationTravelDataComplete()
    {
        return !empty($this->location) &&
            !empty($this->travelDetails);
    }
}
