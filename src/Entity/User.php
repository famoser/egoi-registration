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
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\TimeTrait;
use App\Entity\Traits\UserTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseEntity implements UserInterface
{
    use IdTrait;
    use TimeTrait;
    use UserTrait;

    // can use any features & impersonate users
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    // can use any features
    public const ROLE_COUNTRY = 'ROLE_COUNTRY';

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isAdminAccount = false;

    /**
     * @var Delegation|null
     *
     * @ORM\ManyToOne(targetEntity="ConstructionSite", inversedBy="user")
     */
    private $delegation;

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {
        if ($this->isAdminAccount) {
            return [self::ROLE_ADMIN];
        }

        return [self::ROLE_COUNTRY];
    }

    /**
     * @return bool
     */
    public function isAdminAccount(): bool
    {
        return $this->isAdminAccount;
    }

    /**
     * @param bool $isAdminAccount
     */
    public function setIsAdminAccount(bool $isAdminAccount): void
    {
        $this->isAdminAccount = $isAdminAccount;
    }

    /**
     * @return Delegation|null
     */
    public function getDelegation(): ?Delegation
    {
        return $this->delegation;
    }

    /**
     * @param Delegation|null $delegation
     */
    public function setDelegation(?Delegation $delegation): void
    {
        $this->delegation = $delegation;
    }
}
