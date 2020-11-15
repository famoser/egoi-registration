<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Delegation;

use App\Form\Traits\EditDelegationAttendanceType;
use App\Form\Traits\EditDelegationContributionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditDelegationType extends AbstractDelegationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);

        $builder->add('attendance', EditDelegationAttendanceType::class, ['inherit_data' => true, 'label' => 'trait.name']);
        $builder->add('contribution', EditDelegationContributionType::class, ['inherit_data' => true, 'label' => 'trait.name']);
    }
}
