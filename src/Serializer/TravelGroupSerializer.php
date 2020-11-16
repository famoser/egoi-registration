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

use App\Entity\TravelGroup;
use App\Enum\ArrivalOrDeparture;
use App\Enum\ReviewProgress;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

class TravelGroupSerializer implements ContextAwareNormalizerInterface
{
    private $translator;
    private $normalizer;

    public function __construct(TranslatorInterface $translator, ObjectNormalizer $normalizer)
    {
        $this->translator = $translator;
        $this->normalizer = $normalizer;
    }

    /**
     * @param TravelGroup $travelGroup
     */
    public function normalize($travelGroup, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($travelGroup, $format, $context);

        if (isset($data['arrivalOrDeparture'])) {
            $data['arrivalOrDeparture'] = ArrivalOrDeparture::getTranslationForValue($data['arrivalOrDeparture'], $this->translator);
        }

        if (isset($data['reviewProgress'])) {
            $data['reviewProgress'] = ReviewProgress::getTranslationForValue($data['reviewProgress'], $this->translator);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof TravelGroup;
    }
}
