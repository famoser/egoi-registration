<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Extension;

use App\Enum\ArrivalOrDeparture;
use App\Enum\BooleanType;
use App\Enum\ParticipantRole;
use App\Enum\ParticipationMode;
use App\Helper\DateTimeFormatter;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MyTwigExtension extends AbstractExtension
{
    private $translator;
    private $request;

    public function __construct(TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * makes the filters available to twig.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('dateFormat', [$this, 'dateFormatFilter']),
            new TwigFilter('dateTimeFormat', [$this, 'dateTimeFormatFilter']),
            new TwigFilter('booleanFormat', [$this, 'booleanFilter']),
            new TwigFilter('camelCaseToUnderscore', [$this, 'camelCaseToUnderscoreFilter']),
            new TwigFilter('transParticipantRole', [$this, 'transParticipantRole']),
            new TwigFilter('transArrivalOrDeparture', [$this, 'transArrivalOrDeparture']),
            new TwigFilter('transParticipationMode', [$this, 'transParticipationMode']),
            new TwigFilter('truncate', [$this, 'truncateFilter'], ['needs_environment' => true]),
        ];
    }

    public function camelCaseToUnderscoreFilter(string $propertyName): string
    {
        return mb_strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $propertyName));
    }

    public function dateFormatFilter(?DateTime $date): string
    {
        if ($date instanceof DateTime) {
            return $date->format(DateTimeFormatter::DATE_FORMAT);
        }

        return '-';
    }

    public function dateTimeFormatFilter(?DateTime $date): string
    {
        if ($date instanceof DateTime) {
            return $this->prependDayName($date).', '.$date->format(DateTimeFormatter::DATE_TIME_FORMAT);
        }

        return '-';
    }

    public function booleanFilter(bool $value): string
    {
        if ($value) {
            return BooleanType::getTranslationForValue(BooleanType::YES, $this->translator);
        }

        return BooleanType::getTranslationForValue(BooleanType::NO, $this->translator);
    }

    public function transParticipantRole(int $value): string
    {
        return ParticipantRole::getTranslationForValue($value, $this->translator);
    }

    public function transArrivalOrDeparture(int $value): string
    {
        return ArrivalOrDeparture::getTranslationForValue($value, $this->translator);
    }

    public function transParticipationMode(int $value)
    {
        return ParticipationMode::getTranslationForValue($value, $this->translator);
    }

    /**
     * @source https://github.com/twigphp/Twig-extensions/blob/master/src/TextExtension.php
     */
    public function truncateFilter(Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
                    return $value;
                }

                $length = $breakpoint;
            }

            return rtrim(mb_substr($value, 0, $length, $env->getCharset())).$separator;
        }

        return $value;
    }

    private function prependDayName(DateTime $date): string
    {
        return $this->translator->trans('date_time.'.$date->format('D'), [], 'framework');
    }
}
