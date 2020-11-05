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

trait DelegationContributionTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $translations;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $contributionReviewProgress = ReviewProgress::NOT_EDITED;

    public function getTranslations(): ?string
    {
        return $this->translations;
    }

    public function setTranslations(?string $translations): void
    {
        $this->translations = $translations;
    }

    public function getContributionReviewProgress(): int
    {
        return $this->contributionReviewProgress;
    }

    public function setContributionReviewProgress(int $contributionReviewProgress): void
    {
        $this->contributionReviewProgress = $contributionReviewProgress;
    }

    public function isDelegationContributionDataComplete()
    {
        return !empty($this->translations);
    }
}
