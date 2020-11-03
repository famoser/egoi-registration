<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\ParticipantTraits;

use App\Enum\ReviewProgress;
use Doctrine\ORM\Mapping as ORM;

trait ImmigrationDataTrait
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
    private $immigrationDataReviewProgress = ReviewProgress::NOT_EDITED;

    /**
     * @return string|null
     */
    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * @param string|null $nationality
     */
    public function setNationality(?string $nationality): void
    {
        $this->nationality = $nationality;
    }

    /**
     * @return string|null
     */
    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    /**
     * @param string|null $passportNumber
     */
    public function setPassportNumber(?string $passportNumber): void
    {
        $this->passportNumber = $passportNumber;
    }

    /**
     * @return \DateTime|null
     */
    public function getPassportValidityFrom(): ?\DateTime
    {
        return $this->passportValidityFrom;
    }

    /**
     * @param \DateTime|null $passportValidityFrom
     */
    public function setPassportValidityFrom(?\DateTime $passportValidityFrom): void
    {
        $this->passportValidityFrom = $passportValidityFrom;
    }

    /**
     * @return string|null
     */
    public function getPassportIssueCountry(): ?string
    {
        return $this->passportIssueCountry;
    }

    /**
     * @param string|null $passportIssueCountry
     */
    public function setPassportIssueCountry(?string $passportIssueCountry): void
    {
        $this->passportIssueCountry = $passportIssueCountry;
    }

    /**
     * @return string|null
     */
    public function getPassportImage(): ?string
    {
        return $this->passportImage;
    }

    /**
     * @param string|null $passportImage
     */
    public function setPassportImage(?string $passportImage): void
    {
        $this->passportImage = $passportImage;
    }

    /**
     * @return string|null
     */
    public function getCountryOfResidence(): ?string
    {
        return $this->countryOfResidence;
    }

    /**
     * @param string|null $countryOfResidence
     */
    public function setCountryOfResidence(?string $countryOfResidence): void
    {
        $this->countryOfResidence = $countryOfResidence;
    }

    /**
     * @return string|null
     */
    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    /**
     * @param string|null $placeOfBirth
     */
    public function setPlaceOfBirth(?string $placeOfBirth): void
    {
        $this->placeOfBirth = $placeOfBirth;
    }

    /**
     * @return int
     */
    public function getImmigrationDataReviewProgress(): int
    {
        return $this->immigrationDataReviewProgress;
    }

    /**
     * @param int $immigrationDataReviewProgress
     */
    public function setImmigrationDataReviewProgress(int $immigrationDataReviewProgress): void
    {
        $this->immigrationDataReviewProgress = $immigrationDataReviewProgress;
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
