<?php

/*
 * This file is part of the famoser/egoi-registration project.
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
     * @param Participant $travelGroup
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($travelGroup, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($travelGroup, $format, $context);

        if (isset($data['diet'])) {
            $data['diet'] = Diet::getTranslationForValue($data['diet'], $this->translator);
        }

        if (isset($data['eventPresenceReviewProgress'])) {
            $data['eventPresenceReviewProgress'] = ReviewProgress::getTranslationForValue($data['eventPresenceReviewProgress'], $this->translator);
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
