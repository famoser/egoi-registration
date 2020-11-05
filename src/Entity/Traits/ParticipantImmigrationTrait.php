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

trait ParticipantImmigrationTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $nationality;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $passportNumber;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $passportValidityFrom;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $passportValidityTo;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $passportIssueCountry;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $passportImage;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $countryOfResidence;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $placeOfBirth;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $immigrationReviewProgress = ReviewProgress::NOT_EDITED;

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): void
    {
        $this->nationality = $nationality;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(?string $passportNumber): void
    {
        $this->passportNumber = $passportNumber;
    }

    public function getPassportValidityFrom(): ?\DateTime
    {
        return $this->passportValidityFrom;
    }

    public function setPassportValidityFrom(?\DateTime $passportValidityFrom): void
    {
        $this->passportValidityFrom = $passportValidityFrom;
    }

    public function getPassportIssueCountry(): ?string
    {
        return $this->passportIssueCountry;
    }

    public function setPassportIssueCountry(?string $passportIssueCountry): void
    {
        $this->passportIssueCountry = $passportIssueCountry;
    }

    public function getPassportImage(): ?string
    {
        return $this->passportImage;
    }

    public function setPassportImage(?string $passportImage): void
    {
        $this->passportImage = $passportImage;
    }

    public function getCountryOfResidence(): ?string
    {
        return $this->countryOfResidence;
    }

    public function setCountryOfResidence(?string $countryOfResidence): void
    {
        $this->countryOfResidence = $countryOfResidence;
    }

    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    public function setPlaceOfBirth(?string $placeOfBirth): void
    {
        $this->placeOfBirth = $placeOfBirth;
    }

    public function getImmigrationReviewProgress(): int
    {
        return $this->immigrationReviewProgress;
    }

    public function setImmigrationReviewProgress(int $immigrationReviewProgress): void
    {
        $this->immigrationReviewProgress = $immigrationReviewProgress;
    }

    public function isImmigrationDataComplete()
    {
        return !empty($this->nationality) &&
            !empty($this->passportNumber) &&
            !empty($this->passportValidityFrom) &&
            !empty($this->passportValidityTo) &&
            !empty($this->passportIssueCountry) &&
            !empty($this->passportImage) &&
            !empty($this->countryOfResidence) &&
            !empty($this->placeOfBirth);
    }
}
