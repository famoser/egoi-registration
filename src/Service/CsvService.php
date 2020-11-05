<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Service\Interfaces\CsvServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvService implements CsvServiceInterface
{
    const DELIMITER = ',';

    /**
     * creates a response containing the data rendered as a csv.
     *
     * @param string[]   $header
     * @param string[][] $data
     */
    public function renderCsv(string $filename, array $data, array $header = null): Response
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($header, $data) {
            $handle = fopen('php://output', 'w+');

            //UTF-8 BOM
            fwrite($handle, "\xEF\xBB\xBF");
            //set delimiter to specified
            fwrite($handle, 'sep='.static::DELIMITER."\n");

            if (\is_array($header)) {
                // Add the header of the CSV file
                fputcsv($handle, $header, static::DELIMITER);
            }

            //add the data
            foreach ($data as $row) {
                fputcsv(
                    $handle, // The file pointer
                    $row, // The fields
                    static::DELIMITER // The delimiter
                );
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }
}
