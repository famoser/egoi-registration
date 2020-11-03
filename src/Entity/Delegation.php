<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\DelegationTraits\ContributionDataTrait;
use App\Entity\DelegationTraits\ParticipationDataTrait;
use App\Entity\DelegationTraits\TravelDataTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TimeTrait;
use App\Helper\HashHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Delegation extends BaseEntity
{
    use IdTrait;
    use TimeTrait;

    use ContributionDataTrait;
    use ParticipationDataTrait;
    use TravelDataTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $registrationCode;

    /**
     * @var User[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="country")
     */
    private $users;

    /**
     * @var Delegation[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Participant", mappedBy="country")
     */
    private $participants;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }

    /**
     * @ORM\PrePersist()
     */
    public function generateRegistrationCode()
    {
        $this->registrationCode = HashHelper::getHash();
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return Delegation[]|ArrayCollection
     */
    public function getParticipants()
    {
        return $this->participants;
    }
}
