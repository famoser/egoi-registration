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

use App\Entity\Delegation;
use App\Enum\ParticipationMode;
use App\Enum\ReviewProgress;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\Translation\TranslatorInterface;

class DelegationSerializer implements ContextAwareNormalizerInterface
{
    private $router;
    private $translator;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, TranslatorInterface $translator, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->normalizer = $normalizer;
    }

    /**
     * @param Delegation $travelGroup
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($travelGroup, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($travelGroup, $format, $context);

        if ('delegation-export' === $context['groups']) {
            $data['registration_url'] = $this->router->generate('register', [
                'delegationName' => $travelGroup->getName(),
                'registrationHash' => $travelGroup->getRegistrationHash(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        if (isset($data['participationMode'])) {
            $data['participationMode'] = ParticipationMode::getTranslationForValue($data['participationMode'], $this->translator);
        }

        if (isset($data['attendanceReviewProgress'])) {
            $data['attendanceReviewProgress'] = ReviewProgress::getTranslationForValue($data['attendanceReviewProgress'], $this->translator);
        }

        if (isset($data['contributionReviewProgress'])) {
            $data['contributionReviewProgress'] = ReviewProgress::getTranslationForValue($data['contributionReviewProgress'], $this->translator);
        }

        if (isset($data['travelDetailsReviewProgress'])) {
            $data['travelDetailsReviewProgress'] = ReviewProgress::getTranslationForValue($data['travelDetailsReviewProgress'], $this->translator);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Delegation;
    }
}
