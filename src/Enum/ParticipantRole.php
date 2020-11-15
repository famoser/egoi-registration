<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enum;

use App\Enum\Base\BaseEnum;

class ParticipantRole extends BaseEnum
{
    const LEADER = 0;
    const DEPUTY_LEADER = 1;
    const CONTESTANT = 2;
    const GUEST = 3;
}
