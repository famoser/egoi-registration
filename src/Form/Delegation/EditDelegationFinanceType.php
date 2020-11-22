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

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class EditDelegationFinanceType extends AbstractDelegationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alreadyPayed', NumberType::class, ['translation_domain' => 'trait_delegation_attendance']);
    }
}
