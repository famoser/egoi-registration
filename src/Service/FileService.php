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

use App\Entity\Delegation;
use App\Entity\Participant;
use App\Enum\ParticipantRole;
use App\Service\Interfaces\FileServiceInterface;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipArchive;

class FileService implements FileServiceInterface
{
    /**
     * @var string
     */
    private $persistentDir;

    /**
     * @var string
     */
    private $transientDir;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FileService constructor.
     */
    public function __construct(string $persistentDir, string $transientDir, SluggerInterface $slugger, LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->persistentDir = $persistentDir;
        $this->transientDir = $transientDir;
        $this->slugger = $slugger;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    public function uploadPortrait(Participant $participant, UploadedFile $file): bool
    {
        $folder = $this->getOrCreateDelegationFolder(self::PORTRAIT, $participant->getDelegation());

        $filename = $this->getFilename(self::PORTRAIT, $participant);
        $newFilename = sprintf('%s_%s.%s', $filename, uniqid(), $file->guessExtension());

        // Move the file to the directory where brochures are stored
        try {
            $file->move($folder, $newFilename);
        } catch (FileException $e) {
            $this->logger->error('failed to save file.'.$e->getMessage(), ['exception' => $e]);

            return false;
        }

        // remove old
        if ($participant->getPortrait()) {
            unlink($folder.'/'.$participant->getPortrait());
        }

        $participant->setPortrait($newFilename);

        return true;
    }

    public function download(Participant $participant, string $type, string $filename): Response
    {
        if (self::PORTRAIT === $type) {
            return $this->downloadPortrait($participant, $filename);
        }

        throw new NotFoundHttpException();
    }

    public function downloadAll(string $type): Response
    {
        if (!in_array($type, [self::PORTRAIT])) {
            throw new NotFoundHttpException();
        }

        $folder = $this->getOrCreateFolder($type);
        $targetFile = $this->transientDir.'/'.$type.'.zip';

        $this->zipFolder($folder, $targetFile);

        $response = new BinaryFileResponse($targetFile);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }

    private function downloadPortrait(Participant $participant, string $filename): Response
    {
        if ($participant->getPortrait() !== $filename) {
            throw new NotFoundHttpException();
        }

        $folder = $this->getOrCreateDelegationFolder(self::PORTRAIT, $participant->getDelegation());
        $filePath = $folder.'/'.$filename;

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException();
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }

    private function getOrCreateFolder(string $type): string
    {
        return $this->persistentDir.'/'.$type;
    }

    private function getOrCreateDelegationFolder(string $type, Delegation $delegation): string
    {
        $targetDir = $this->getOrCreateFolder($type).'/'.$delegation->getName();
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        return $targetDir;
    }

    private function getFilename(string $type, Participant $participant): string
    {
        $parts = [
            $participant->getDelegation()->getName(),
            ParticipantRole::getTranslationForValue($participant->getRole(), $this->translator),
            $participant->getName(),
            $type,
        ];

        foreach ($parts as &$part) {
            $part = $this->slugger->slug($part);
        }

        return implode('_', $parts);
    }

    private function zipFolder(string $folder, string $targetPath)
    {
        // Get real path for our folder
        $rootPath = realpath($folder);

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($targetPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
    }
}
