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

use App\Entity\Delegation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DelegationSerializer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    /**
     * @param Delegation $delegation
     */
    public function normalize($delegation, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($delegation, $format, $context);

        if ('delegation-export' === $context['groups']) {
            $data['registration_url'] = $this->router->generate('register', [
                'delegation' => $delegation->getName(),
                'registrationHash' => $delegation->getRegistrationHash(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Delegation;
    }
}
