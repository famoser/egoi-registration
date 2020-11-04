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

trait ParticipantPersonalDataTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $givenName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $familyName;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $birthday;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $phone;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $personalDataReviewProgress = ReviewProgress::NOT_EDITED;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPersonalDataReviewProgress(): int
    {
        return $this->personalDataReviewProgress;
    }

    public function setPersonalDataReviewProgress(int $personalDataReviewProgress): void
    {
        $this->personalDataReviewProgress = $personalDataReviewProgress;
    }

    public function isPersonalDataComplete()
    {
        return !empty($this->givenName) &&
            !empty($this->familyName) &&
            !empty($this->birthday) &&
            !empty($this->email) &&
            !empty($this->phone);
    }
}
