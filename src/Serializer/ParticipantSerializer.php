<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Serializer;

use App\Entity\Participant;
use App\Enum\Diet;
use App\Enum\ParticipantRole;
use App\Enum\ReviewProgress;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ParticipantSerializer implements ContextAwareNormalizerInterface
{
    private $translator;
    private $normalizer;

    public function __construct(TranslatorInterface $translator, ObjectNormalizer $normalizer)
    {
        $this->translator = $translator;
        $this->normalizer = $normalizer;
    }

    /**
     * @param Participant $participant
     */
    public function normalize($participant, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($participant, $format, $context);

        if (isset($data['diet'])) {
            $data['diet'] = Diet::getTranslationForValue($data['diet'], $this->translator);
        }

        if (isset($data['eventAttendanceReviewProgress'])) {
            $data['eventAttendanceReviewProgress'] = ReviewProgress::getTranslationForValue($data['eventAttendanceReviewProgress'], $this->translator);
        }

        if (isset($data['immigrationReviewProgress'])) {
            $data['immigrationReviewProgress'] = ReviewProgress::getTranslationForValue($data['immigrationReviewProgress'], $this->translator);
        }

        if (isset($data['personalDataReviewProgress'])) {
            $data['personalDataReviewProgress'] = ReviewProgress::getTranslationForValue($data['personalDataReviewProgress'], $this->translator);
        }

        if (isset($data['role'])) {
            $data['role'] = ParticipantRole::getTranslationForValue($data['role'], $this->translator);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Participant;
    }
}
