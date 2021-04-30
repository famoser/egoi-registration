<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EuropeanLanguageType extends AbstractType
{
    public const LANGUAGES = [
        'Afrikaans' => 'af',
        'Albanian' => 'sq',
        'Arabic' => 'ar',
        'Armenian' => 'hy',
        'Azerbaijani' => 'az',
        'Belarusian' => 'be',
        'Bosnian' => 'bs',
        'Bulgarian' => 'bg',
        'Burmese' => 'my',
        'Cantonese' => 'yue',
        'Chinese' => 'zh',
        'Croatian' => 'hr',
        'Czech' => 'cs',
        'Danish' => 'da',
        'Dutch' => 'nl',
        'English' => 'en',
        'Esperanto' => 'eo',
        'Estonian' => 'et',
        'Filipino' => 'fil',
        'Finnish' => 'fi',
        'French' => 'fr',
        'Georgian' => 'ka',
        'German' => 'de',
        'Greek' => 'el',
        'Hebrew' => 'he',
        'Hindi' => 'hi',
        'Hungarian' => 'hu',
        'Icelandic' => 'is',
        'Indonesian' => 'id',
        'Irish' => 'ga',
        'Italian' => 'it',
        'Japanese' => 'ja',
        'Klingon' => 'tlh',
        'Korean' => 'ko',
        'Kurdish' => 'ku',
        'Latin' => 'la',
        'Latvian' => 'lv',
        'Lithuanian' => 'lt',
        'Luxembourgish' => 'lb',
        'Macedonian' => 'mk',
        'Maltese' => 'mt',
        'Mongolian' => 'mn',
        'Norwegian' => 'no',
        'Persian' => 'fa',
        'Polish' => 'pl',
        'Portuguese' => 'pt',
        'Romanian' => 'ro',
        'Romansh' => 'rm',
        'Russian' => 'ru',
        'Serbian' => 'sr',
        'Slovak' => 'sk',
        'Slovenian' => 'sl',
        'Spanish' => 'es',
        'Swedish' => 'sv',
        'Swiss German' => 'gsw',
        'Tamil' => 'ta',
        'Thai' => 'th',
        'Tibetan' => 'bo',
        'Turkish' => 'tr',
        'Ukrainian' => 'uk',
        'Uzbek' => 'uz',
        'Vietnamese' => 'vi',
    ];

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => self::LANGUAGES,
            'choice_translation_domain' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'european_language';
    }
}
