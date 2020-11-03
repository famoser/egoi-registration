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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/*
 * Personal information + email from UserTrait
 */

trait AddressTrait
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
    private $phone;

    /**
     * @return string|null
     */
    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    /**
     * @param string|null $givenName
     */
    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string|null
     */
    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    /**
     * @param string|null $familyName
     */
    public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime|null $birthday
     */
    public function setBirthday(?\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
}
