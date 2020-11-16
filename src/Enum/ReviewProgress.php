<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enum;

use App\Enum\Base\BaseEnum;

class ReviewProgress extends BaseEnum
{
    const NOT_EDITED = 0;
    const EDITED = 1;
    const REVIEWED_AND_LOCKED = 2;
}
