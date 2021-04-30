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
     * @var string[]
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $languages;

    /**
     * @var string|null
     *
     * @Groups({"delegation-export"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $deliveryAddress;

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

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?string $deliveryAddress): void
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    public function isContributionComplete()
    {
        return ReviewProgress::NOT_EDITED !== $this->contributionReviewProgress && $this->deliveryAddress;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param string[] $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }
}
