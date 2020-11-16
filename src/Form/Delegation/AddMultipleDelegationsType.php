<?php

/*
 * This file is part of the famoser/egoi-registration project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Delegation;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddMultipleDelegationsType extends AbstractDelegationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('commaSeparatedDelegationNames', TextType::class, ['mapped' => false, 'help' => 'comma_separated_delegation_names_help']);
    }
}
