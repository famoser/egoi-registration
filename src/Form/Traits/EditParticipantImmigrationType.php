<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Traits;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditParticipantImmigrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nationality', TextType::class, ['required' => false]);
        $builder->add('passportNumber', TextType::class, ['required' => false]);
        $builder->add('passportValidityFrom', DateType::class, ['widget' => 'single_text', 'required' => false]);
        $builder->add('passportValidityTo', DateType::class, ['widget' => 'single_text', 'required' => false]);
        $builder->add('passportIssueCountry', TextType::class, ['required' => false]);
        $builder->add('countryOfResidence', TextType::class, ['help' => 'country_of_residence_help', 'required' => false]);
        $builder->add('placeOfBirth', TextType::class, ['help' => 'place_of_birth_help', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'trait_participant_immigration',
            'data_class' => Participant::class,
        ]);
    }
}
