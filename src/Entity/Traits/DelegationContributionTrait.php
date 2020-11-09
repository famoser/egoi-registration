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

trait DelegationContributionTrait
{
    /**
     * @var string[]
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $translations;

    /**
     * @var int
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="integer")
     */
    private $contributionReviewProgress = ReviewProgress::NOT_EDITED;

    /**
     * @return string[]
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @param string[] $translations
     */
    public function setTranslations(array $translations): void
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

    public function isContributionComplete()
    {
        return ReviewProgress::NOT_EDITED !== $this->contributionReviewProgress;
    }
}
