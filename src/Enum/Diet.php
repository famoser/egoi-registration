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

class Diet extends BaseEnum
{
    const NONE = 0;
    const VEGETARIAN = 1;
    const VEGAN = 2;

    const HALAL = 3;
    const KOSHER = 4;
    const LACTO_VEGETARIAN = 5;
}
