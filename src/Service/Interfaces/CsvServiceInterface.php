<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Interfaces;

use Symfony\Component\HttpFoundation\Response;

interface CsvServiceInterface
{
    /**
     * creates a response containing the data rendered as a csv.
     *
     * @param string[]   $header
     * @param string[][] $data
     */
    public function renderCsv(string $filename, array $data, array $header = null): Response;
}
