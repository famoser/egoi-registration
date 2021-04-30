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

class EuropeanLanguageWithProficiencyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $proficiencyLanguages = EuropeanLanguageType::LANGUAGES;
        unset($proficiencyLanguages['English']);
        $proficiencyLanguages['English (fluent)'] = 'en';
        $proficiencyLanguages['English (basic)'] = 'en-basiceng';

        ksort($proficiencyLanguages);

        $resolver->setDefaults([
            'choices' => $proficiencyLanguages,
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
