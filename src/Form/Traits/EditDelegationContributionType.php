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

use App\Entity\Delegation;
use App\Form\EuropeanLanguageType;
use App\Form\EuropeanLanguageWithProficiencyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDelegationContributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('translations', EuropeanLanguageType::class, ['required' => false, 'multiple' => true, 'help' => 'translations_help']);
        $builder->add('languages', EuropeanLanguageWithProficiencyType::class, ['required' => false, 'multiple' => true, 'help' => 'languages_help']);
        $builder->add('deliveryAddress', TextareaType::class, ['required' => false, 'help' => 'delivery_address_help']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'trait_delegation_contribution',
            'data_class' => Delegation::class,
        ]);
    }
}
