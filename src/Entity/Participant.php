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
use App\Entity\ParticipantTraits\EventDataTrait;
use App\Entity\ParticipantTraits\ImmigrationDataTrait;
use App\Entity\ParticipantTraits\PersonalDataTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TimeTrait;
use App\Enum\ParticipantRole;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Participant extends BaseEntity
{
    use IdTrait;
    use TimeTrait;

    use PersonalDataTrait;
    use EventDataTrait;
    use ImmigrationDataTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $role = ParticipantRole::ATHLETE;

    /**
     * @var Delegation
     *
     * @ORM\ManyToOne(targetEntity="Delegation", inversedBy="participants")
     */
    private $country;

    public function getCountry(): Delegation
    {
        return $this->country;
    }

    public function setCountry(Delegation $country): void
    {
        $this->country = $country;
    }
}
