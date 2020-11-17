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

use App\Enum\Gender;
use App\Enum\ReviewProgress;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ParticipantPersonalDataTrait
{
    /**
     * @var string|null
     *
     * @Groups({"participant-export", "travel-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $givenName;

    /**
     * @var string|null
     *
     * @Groups({"participant-export", "travel-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $familyName;

    /**
     * @var \DateTime|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $email;

    /**
     * @var int|null
     *
     * @Groups({"participant-export", "travel-export"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gender = Gender::FEMALE;

    /**
     * @var string|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $nameOnDocuments;

    /**
     * @var string|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $portrait;

    /**
     * @var string|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $papers;

    /**
     * @var string|null
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $consent;

    /**
     * @var int
     *
     * @Groups({"participant-export"})
     * @ORM\Column(type="integer")
     */
    private $personalDataReviewProgress = ReviewProgress::NOT_EDITED;

    public function getName(): string
    {
        return trim($this->getGivenName().' '.$this->getFamilyName());
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName;
    }

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): void
    {
        $this->gender = $gender;
    }

    public function getPersonalDataReviewProgress(): int
    {
        return $this->personalDataReviewProgress;
    }

    public function setPersonalDataReviewProgress(int $personalDataReviewProgress): void
    {
        $this->personalDataReviewProgress = $personalDataReviewProgress;
    }

    public function getNameOnDocuments(): ?string
    {
        return $this->nameOnDocuments;
    }

    public function setNameOnDocuments(?string $nameOnDocuments): void
    {
        $this->nameOnDocuments = $nameOnDocuments;
    }

    public function getPortrait(): ?string
    {
        return $this->portrait;
    }

    public function setPortrait(?string $portrait): void
    {
        $this->portrait = $portrait;
    }

    public function getPapers(): ?string
    {
        return $this->papers;
    }

    public function setPapers(?string $papers): void
    {
        $this->papers = $papers;
    }

    public function getConsent(): ?string
    {
        return $this->consent;
    }

    public function setConsent(?string $consent): void
    {
        $this->consent = $consent;
    }

    public function isPersonalDataComplete()
    {
        $validation = !empty($this->givenName) &&
            !empty($this->familyName) &&
            !empty($this->birthday) &&
            !empty($this->gender) &&
            !empty($this->portrait) &&
            !empty($this->nameOnDocuments) &&
            !empty($this->papers) &&
            !empty($this->consent);

        if ($this->isLeader()) {
            $validation &= !empty($this->email);
        }

        return $validation;
    }
}
