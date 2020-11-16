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

use App\Entity\Participant;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

interface FileServiceInterface
{
    public const PORTRAIT = 'portrait';

    public function uploadPortrait(Participant $participant, UploadedFile $file): bool;

    public function downloadAllPortrait(): Response;

    public function download(Participant $participant, string $type, string $filename): Response;
}
