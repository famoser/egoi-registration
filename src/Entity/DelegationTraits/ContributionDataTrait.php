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

trait ContributionDataTrait
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
    private $contributionDataReviewProgress = ReviewProgress::NOT_EDITED;

    /**
     * @return string|null
     */
    public function getTranslations(): ?string
    {
        return $this->translations;
    }

    /**
     * @param string|null $translations
     */
    public function setTranslations(?string $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return int
     */
    public function getContributionDataReviewProgress(): int
    {
        return $this->contributionDataReviewProgress;
    }

    /**
     * @param int $contributionDataReviewProgress
     */
    public function setContributionDataReviewProgress(int $contributionDataReviewProgress): void
    {
        $this->contributionDataReviewProgress = $contributionDataReviewProgress;
    }

    public function isDelegationContributionDataComplete()
    {
        return !empty($this->translations);
    }
}
